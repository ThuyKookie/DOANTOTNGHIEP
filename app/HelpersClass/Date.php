<?php

namespace App\HelpersClass;
use DateTime;
use DatePeriod;
use DateInterval;
class Date
{
    public static function getListDayInMonth()
    {
        $arrayDay = [];
        $month    = date('m');
        $year     = date('Y');
        // Lấy tất cả các ngày trong tháng
        for ($day =  1; $day <= 31 ; $day ++)
        {
            $time = mktime(12,0,0, $month, $day, $year);
                if (date('m', $time) == $month)
                    $arrayDay[] = date('Y-m-d', $time);
        }

        return $arrayDay;
    }
    public static function getListDayInTowDay($day1, $day2)
    {
        $arrayDay = [];
        $begin = new DateTime($day1);
        $end = new DateTime($day2);

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        foreach ($period as $dt) {
            //echo $dt->format("l Y-m-d H:i:s\n");
            $arrayDay[] = $dt->format("Y-m-d");
        }

        return $arrayDay;
    }
}
