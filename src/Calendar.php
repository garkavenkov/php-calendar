<?php

namespace Garkavenkov\Calendar;

class Calendar
{    
    private $calendar_date;
    private $first_day_of_month;
    private $last_day_of_month;    
    private $calendar = [];
   
    
    public function __construct(int $year = null, int $month = null, int $day=1, bool $week_begins_on_monday = true)    
    {
        // Date initialization
        if (!is_null($year) && !is_null($month)) {                         
            $this->calendar_date = getdate(mktime(0, 0, 0, $month, $day, $year));
        } else {        
            $this->calendar_date = getdate();            
        }        
        // print_r($this->calendar_date);
        $this->calendar['info']['month']['index'] = $this->calendar_date['mon'];
        $this->calendar['info']['month']['name'] = $this->calendar_date['month'];
        $this->calendar['info']['year'] = $this->calendar_date['year'];
        
        // First day of month
        $this->first_day_of_month = getdate(mktime(0, 0, 0, $this->calendar_date['mon'], 1, $this->calendar_date['year']));
        
        // First day of month
        $this->last_day_of_month = getdate(mktime(0, 0, 0, $this->calendar_date['mon']+1, 0, $this->calendar_date['year']));
        // print_r($this->last_day_of_month);

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
                // $incl_next_month_days = 0;
            }
            // } else {
            //     $incl_next_month_days = 7 - $month_ends_on;
            // }
        } else {
            $rest_prev_month_days = $month_starts_on;
            // $incl_next_month_days = 6 - $month_ends_on;
        }

        // First week
        $days = [];
        $day_of_month = 1;
        if ($rest_prev_month_days > 0) {
            $week_number = date('W',mktime(0,0,0,$this->calendar_date['mon'], $this->first_day_of_month['mday'], $this->calendar_date['year']));
            for ($i = $rest_prev_month_days-1; $i>=0; $i--) {
                $days[] = date('Y-m-d',mktime(0,0,0,$this->calendar_date['mon'], -1 * $i, $this->calendar_date['year']));
            }
            for ($i = 1; $i < (7-$rest_prev_month_days) + 1; $i++, $day_of_month++) {
                $days[] = date('Y-m-d',mktime(0,0,0,$this->calendar_date['mon'], $i, $this->calendar_date['year']));
            }
            // $this->weeks[$week_number]['days'] = $days;
            // $this->calendar['weeks'][$week_number] = $days;
            $this->calendar['weeks'][] = ['number' => $week_number, 'days' => $days] ;
            $days = [];
        }
        // echo "Day of month index: $day_of_month\t";
        
        // Rest weeks of the month
        $end_of_week = $day_of_month + 7;   
        // echo "End week: $end_of_week\n";     
        while($day_of_month <= $this->last_day_of_month['mday']) {

            $week_number = date('W',mktime(0,0,0,$this->calendar_date['mon'], $day_of_month, $this->calendar_date['year']));

            for($i = $day_of_month; $i <$end_of_week; $i++) {                
                $days[] = date('Y-m-d',mktime(0,0,0,$this->calendar_date['mon'], $i, $this->calendar_date['year']));
            }

            // $this->weeks[$week_number]['days'] = $days;            
            $this->calendar['weeks'][] = ['number' => $week_number, 'days' => $days] ;
            $days = [];
            $day_of_month = $end_of_week ;
            $end_of_week = $day_of_month + 7;
            // echo "Day of month index: $day_of_month\t";
            // echo "End week: $end_of_week\n";     
        }        
    }

    public function get()
    {
        return $this->calendar;
    }

    public function getMonthBoundry(): array
    {
        return ['start' => $this->first_day_of_month, 'end' => $this->last_day_of_month];
    }

    public function getCalendarBoundry(): array
    {
        return ['start' => $this->calendar['weeks'][0]['days'][0], 'end' => $this->calendar['weeks'][count($this->calendar['weeks'])-1]['days'][6] ];
    }
}
