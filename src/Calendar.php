<?php

namespace Garkavenkov\Calendar;

use Garkavenkov\Calendar\Localization;

class Calendar
{   
    private $calendar = [];    
    private $first_day_of_month;
    private $last_day_of_month;    
    private $week_since_monday;        
    private $localization = null;
   
    /**
     * Format calendar day
     *
     * @param integer $year     Calendar year
     * @param integer $month    Calendar month
     * @param integer $day      Calendar day
     * @return void
     */
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
    
    /**
     * Constructor
     *
     * @param integer|null $year                Calendar year
     * @param integer|null $month               Calendar month
     * @param boolean $week_begins_on_monday    Week begins on Monday or Sunday
     * @param string $lang                      Language string code, i.e. "ua"
     */
    public function __construct(int $year = null, int $month = null, bool $week_begins_on_monday = false, string $lang = 'en')
    {   
        // Date initialization
        $calendar_date = null;        
        if (!is_null($year) && !is_null($month)) {                         
            $calendar_date = getdate(mktime(0, 0, 0, $month, 1, $year));
        } else {        
            $calendar_date = getdate();            
        }        

        $this->week_since_monday = $week_begins_on_monday;
        
        $this->localization = new Localization($lang);        

        // Days' names
        $this->daysName($lang);

        // Months' names
        $this->monthsName($lang);

        $this->calendar['info']['month']['index'] = $calendar_date['mon'];
        $this->calendar['info']['month']['name'] = $this->localization->getMonthName($calendar_date['month']);
        $this->calendar['info']['year'] = $calendar_date['year'];

        // First day of month
        $this->first_day_of_month = $this->day(year: $calendar_date['year'], month: $calendar_date['mon'], day: 1);
        
        // Last day of month        
        $this->last_day_of_month = $this->day(year: $calendar_date['year'], month: $calendar_date['mon']+1, day: 0);

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
            $week_number = date('W',mktime(0,0,0,$calendar_date['mon'], $this->first_day_of_month['mday'], $calendar_date['year']));
            for ($i = $rest_prev_month_days-1; $i>=0; $i--) {                
                $days[] = $this->day(year: $calendar_date['year'], month: $calendar_date['mon'], day: -1 * $i);
            }
            for ($i = 1; $i < (7-$rest_prev_month_days) + 1; $i++, $day_of_month++) {             
                $days[] = $this->day(year: $calendar_date['year'], month: $calendar_date['mon'], day: $i);
            }         
            $this->calendar['weeks'][] = ['number' => $week_number, 'days' => $days] ;
            $days = [];
        }        
        
        // Rest weeks of the month
        $end_of_week = $day_of_month + 7;           
        while($day_of_month <= $this->last_day_of_month['mday']) {

            $week_number = date('W',mktime(0,0,0,$calendar_date['mon'], $day_of_month, $calendar_date['year']));

            for($i = $day_of_month; $i <$end_of_week; $i++) {                                
                $days[] = $this->day(year: $calendar_date['year'], month: $calendar_date['mon'], day: $i);
            }
            
            $this->calendar['weeks'][] = ['number' => $week_number, 'days' => $days] ;
            $days = [];
            $day_of_month = $end_of_week ;
            $end_of_week = $day_of_month + 7;
        }
    }

    /**
     * Generating the names of the days of the week.
     *
     * @param string $lang Language string code, i.e. "ua"
     * @return void
     */
    private function daysName(string $lang)
    {
        $week_begin = $this->week_since_monday ? "monday" : "sunday";        
        for ($i = 0; $i < 7; $i++) {                        
            $day = date("l", strtotime("last $week_begin +$i day"));
            $this->calendar['info']['weekDayNames'][] = $lang != 'en' ? $this->localization->getDayName($day) : $day;
        }
    }

    /**
     * Generate of month names
     *
     * @param string $lang Language string code, i.e. "ua"
     * @return void
     */
    private function monthsName(string $lang)
    {
        for ($i = 1; $i <= 12; $i++) {
            $month = \DateTime::createFromFormat('!m', $i)->format('F');
            $this->calendar['info']['months'][] = $lang != 'en' ? $this->localization->getMonthName($month) : $month;
        }
    }

    /**
     * Return calendar
     *
     * @return array
     */
    public function get(): array
    {
        return $this->calendar;
    }

    /**
     * Return month boundaries
     *
     * @param string|null $format
     * @return array
     */
    public function getMonthBoundaries(string $format = null): array
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

    /**
     * Return calendar boundaries
     *
     * @param string|null $format
     * @return array
     */
    public function getCalendarBoundaries(string $format = null): array
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
    
    /**
     * Returns days' names
     *
     * @return array
     */
    public function getWeekdays(): array
    {
        return $this->calendar['info']['weekDayNames'];
    }
    
    /**
     * Returns months' names
     *
     * @return array
     */
    public function getMonths(): array
    {
        return $this->calendar['info']['months'];
    }

    /**
     * Inject events into day
     *
     * @param string $title Events name
     * @param array $events 
     * @return void
     */
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

    /**
     * Print to console calendar
     *
     * @return void
     */
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
    
    /**
     * Returns calendar's weeks' numbers
     *
     * @return array
     */
    public function getWeeksNumbers(): array
    {
        $numbers = array_map(function($week) {
            return $week['number'];
        },$this->calendar['weeks']);
        return $numbers;
    }

    /**
     * Returns week by number
     *
     * @param integer $number
     * @return array
     */
    public function getWeek(int $number): array
    {
        return array_filter($this->calendar['weeks'], function($week) use($number) {
            return $week['number'] == $number;
        });        
    }

    /**
     * Returns calendar basic information: year, month name, calendar and month boundaries
     *
     * @param string|null $dateFormat
     * @return array
     */
    public function getCalendarInfo(string $dateFormat = null): array
    {        
        return [
            'year'  =>  $this->calendar['info']['year'],
            'month' =>  $this->calendar['info']['month'],
            'calendarBoundries' =>  $this->getCalendarBoundaries($dateFormat),
            'monthBoundries'    =>  $this->getMonthBoundaries($dateFormat)
        ];
    }

    /**
     * Returns the calendar's day.
     *
     * @param string $date Date in format 'Y-m-d'
     * @return array
     */
    public function getDay(string $date): array
    {
        $calendar_days = [];

        foreach ($this->calendar['weeks'] as $week) {
            foreach($week['days'] as $day) {
                array_push($calendar_days, $day);
            }
        }
        $day = array_filter($calendar_days, function($day) use($date) {
            return $day['date'] == $date;
        });

        return array_values($day);
    }
}
