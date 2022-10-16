<?php

namespace Garkavenkov\Calendar;

class Localization
{
    private $months = [];
    private $days = [];

    public function __construct($lang)
    {
        $file = __DIR__ . '/locale/'. $lang . '.php';
        // echo __DIR__ . PHP_EOL;
        if (file_exists($file)) {
            // echo "File '$file' exists\n";            
            include('locale/'. $lang . '.php');
            if (isset($lang['months'])) {
                $this->months = $lang['months'];
            }
            if (isset($lang['days'])) {
                $this->days = $lang['days'];
            }
        } 
        // print_r($lang);
        
        
        // print_r($this->months);
    }

    public function getMonthName($name) {        
        return isset($this->months[$name])  ? $this->months[$name] : $name;
    }

    public function getDayName($name) {
        return isset($this->days[$name])  ? $this->days[$name] : $name;
    }        
}
