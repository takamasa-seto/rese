<?php

namespace App\Http\Traits;

use App\Consts\RoleConst;

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

    /*
        管理者の場合はtrueを戻す
    */
    public function isAdmin($role)
    {
        if(RoleConst::ADMINISTRATOR == $role) {
            return true;
        } else {
            return false;
        }
    }

    /*
        店舗代表者の場合はtrueを戻す
    */
    public function isShopStaff($role)
    {
        if(RoleConst::SHOP_STAFF == $role) {
            return true;
        } else {
            return false;
        }
    }

}