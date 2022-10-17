<?php

namespace Garkavenkov\Calendar;

class Localization
{
    private $months = [];
    private $days = [];

    public function __construct($lang)
    {
        $file = __DIR__ . '/locale/'. $lang . '.php';
        
        if (file_exists($file)) {        
            include('locale/'. $lang . '.php');
            if (isset($lang['months'])) {
                $this->months = $lang['months'];
            }
            if (isset($lang['days'])) {
                $this->days = $lang['days'];
            }
        }         
    }

    public function getMonthName($name) {        
        return isset($this->months[$name])  ? $this->months[$name] : $name;
    }

    public function getDayName($name) {
        return isset($this->days[$name])  ? $this->days[$name] : $name;
    }        
}
