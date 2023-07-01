<?php

namespace App\Http\Traits;

trait Content {
  
    /*
        時間を分に変換する（プライベート関数）
    */
    public function timeToMin($time)
    {
        $t_array = explode(':', $time);
        $hour = $t_array[0] * 60;
        $second = round($t_array[2] / 60, 2);
        $mins = $hour + $t_array[1] + $second;
        return $mins;
    }

}