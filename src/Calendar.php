<?php

namespace Garkavenkov\Calendar;

use Garkavenkov\Calendar\Localization;

class Calendar
{    
    private $calendar_date;
    private $first_day_of_month;
    private $last_day_of_month;    
    private $calendar = [];
    private $week_since_monday;    
    private $localization = null;
   
    private function day(int $year, int $month, int $day)
    {
        [
            "mday"      => $mday,
            "wday"      => $wday,
            "mon"       => $mon,
            "year"      => $year,
            "yday"      => $yday,
            "weekday"   => $weekday,
            "month"     => $month,
        ] = getdate(mktime(0, 0, 0, $month, $day, $year));        

        $formated_month = $mon <= 9 ? "0" . $mon  : $mon;
        $formated_day = $mday <= 9 ? "0" . $mday  : $mday;

        $date = $year . "-" .  $formated_month . "-" . $formated_day;

        return [
            "date"      =>  $date,
            "mday"      =>  $mday,
            "wday"      =>  $wday,
            "mon"       =>  $mon,
            "year"      =>  $year,
            "yday"      =>  $yday,
            "weekday"   =>  $this->localization->getDayName($weekday),
            "month"     =>  $this->localization->getMonthName($month),
        ];
    }
    
    public function __construct(int $year = null, int $month = null, bool $week_begins_on_monday = false, string $lang = 'en')
    {        
        // Date initialization
        if (!is_null($year) && !is_null($month)) {                         
            $this->calendar_date = getdate(mktime(0, 0, 0, $month, 1, $year));
        } else {        
            $this->calendar_date = getdate();            
        }        

        $this->week_since_monday = $week_begins_on_monday;
        
        $this->localization = new Localization($lang);        

        // Days' names
        $this->daysName($lang);

        // Months' names
        $this->monthsName($lang);

        $this->calendar['info']['month']['index'] = $this->calendar_date['mon'];
        $this->calendar['info']['month']['name'] = $this->localization->getMonthName($this->calendar_date['month']);
        $this->calendar['info']['year'] = $this->calendar_date['year'];

        // First day of month
        $this->first_day_of_month = $this->day(year: $this->calendar_date['year'], month: $this->calendar_date['mon'], day: 1);
        
        // Last day of month        
        $this->last_day_of_month = $this->day(year: $this->calendar_date['year'], month: $this->calendar_date['mon']+1, day: 0);

        // Month start and end days in week
        $month_starts_on = $this->first_day_of_month['wday'];
        $month_ends_on   = $this->last_day_of_month['wday'];

        if ($week_begins_on_monday) {
            if ($month_starts_on == 0) {
                $month_starts_on = 7;
                $rest_prev_month_days = 6;
            } else {
                $rest_prev_month_days = $month_starts_on - 1;
            }
            if ($month_ends_on == 0) {
                $month_ends_on = 7;                
            }            
        } else {
            $rest_prev_month_days = $month_starts_on;
        }

        // First week
        $days = [];
        $day_of_month = 1;
        if ($rest_prev_month_days > 0) {
            $week_number = date('W',mktime(0,0,0,$this->calendar_date['mon'], $this->first_day_of_month['mday'], $this->calendar_date['year']));
            for ($i = $rest_prev_month_days-1; $i>=0; $i--) {                
                $days[] = $this->day(year: $this->calendar_date['year'], month: $this->calendar_date['mon'], day: -1 * $i);
            }
            for ($i = 1; $i < (7-$rest_prev_month_days) + 1; $i++, $day_of_month++) {             
                $days[] = $this->day(year: $this->calendar_date['year'], month: $this->calendar_date['mon'], day: $i);
            }         
            $this->calendar['weeks'][] = ['number' => $week_number, 'days' => $days] ;
            $days = [];
        }        
        
        // Rest weeks of the month
        $end_of_week = $day_of_month + 7;           
        while($day_of_month <= $this->last_day_of_month['mday']) {

            $week_number = date('W',mktime(0,0,0,$this->calendar_date['mon'], $day_of_month, $this->calendar_date['year']));

            for($i = $day_of_month; $i <$end_of_week; $i++) {                                
                $days[] = $this->day(year: $this->calendar_date['year'], month: $this->calendar_date['mon'], day: $i);
            }
            
            $this->calendar['weeks'][] = ['number' => $week_number, 'days' => $days] ;
            $days = [];
            $day_of_month = $end_of_week ;
            $end_of_week = $day_of_month + 7;
        }
    }

    private function daysName($lang) {
        $week_begin = $this->week_since_monday ? "monday" : "sunday";        
        for ($i = 0; $i < 7; $i++) {                        
            $day = date("l", strtotime("last $week_begin +$i day"));
            $this->calendar['info']['weekDayNames'][] = $lang != 'en' ? $this->localization->getDayName($day) : $day;
        }
    }

    private function monthsName($lang)
    {
        for ($i = 1; $i <= 12; $i++) {
            $month = \DateTime::createFromFormat('!m', $i)->format('F');
            $this->calendar['info']['months'][] = $lang != 'en' ? $this->localization->getMonthName($month) : $month;
        }
    }

    public function get()
    {
        return $this->calendar;
    }

    public function getMonthBoundries(string $format = null): array
    {
        if (!is_null($format)) {            
            return [                                
                date($format, mktime(0, 0, 0, $this->first_day_of_month['mon'], $this->first_day_of_month['mday'], $this->first_day_of_month['year'])),
                date($format, mktime(0, 0, 0, $this->last_day_of_month['mon'], $this->last_day_of_month['mday'], $this->last_day_of_month['year'])),
            ];
        }
        return [            
            $this->first_day_of_month, 
            $this->last_day_of_month     
        ];    
    }

    public function getCalendarBoundries(string $format = null): array
    {
        $calendar_begin = $this->calendar['weeks'][0]['days'][0];
        $calendar_end   = $this->calendar['weeks'][count($this->calendar['weeks'])-1]['days'][6];

        if (!is_null($format)) {
            return [                                
                date($format, mktime(0, 0, 0, $calendar_begin['mon'], $calendar_begin['mday'], $calendar_begin['year'])),
                date($format, mktime(0, 0, 0, $calendar_end['mon'], $calendar_end['mday'], $calendar_end['year']))
            ];
        }
        return [            
            $this->calendar['weeks'][0]['days'][0], 
            $this->calendar['weeks'][count($this->calendar['weeks'])-1]['days'][6]
        ];
    }
    
    public function getWeekdays()
    {
        return $this->calendar['info']['weekDayNames'];
    }

    public function getMonths()
    {
        return $this->calendar['info']['months'];
    }

    public function injectIntoDay(string $title, array $events)
    {        
        foreach ($this->calendar['weeks'] as &$week) {
            foreach($week['days'] as &$day) {     
                $date = $day['date'];

                $date_events = array_filter($events, function($event) use ($date) {                    
                    return $event['date']  == $date;
                });
                
                $day[$title] = array_values($date_events);                
            }
        }        
    }    

    public function print()
    {        
        $weekdays_length = array_map('mb_strlen',  $this->calendar['info']['weekDayNames']);
        echo "-";
        for ($i=0; $i < count( $this->calendar['info']['weekDayNames']); $i++) {             
            echo str_pad('',  $weekdays_length[$i]+1  , '-');
        }
        echo "\n";
        echo "|";
        for ($i=0; $i < count( $this->calendar['info']['weekDayNames']); $i++) { 
            $day_length = $weekdays_length[$i] ;            
            echo $this->calendar['info']['weekDayNames'][$i] . "|";
        }
        echo "\n";
        echo "|";
        for ($i=0; $i < count( $this->calendar['info']['weekDayNames']); $i++) {             
            echo str_pad('',  $weekdays_length[$i]  , '-') . '|';
        }
        echo "\n";
        foreach ($this->calendar['weeks'] as $week) {
            echo "|";
            for ($i=0; $i < count($week['days']); $i++) { 
                $day_length = $weekdays_length[$i] ;                
                printf("%{$day_length}s|", $week['days'][$i]['mday']);                
            }
            echo "\n";
        }
        echo "-";
        for ($i=0; $i < count( $this->calendar['info']['weekDayNames']); $i++) {             
            echo str_pad('',  $weekdays_length[$i]+1  , '-');
        }
        echo "\n";
    }
    
    public function getWeeksNumbers()
    {
        $numbers = array_map(function($week) {
            return $week['number'];
        },$this->calendar['weeks']);
        return $numbers;
    }
}
