<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Table;
use Auth;
use DateTime;

class ReserveController extends Controller
{
    /*
        時間を分に変換する（プライベート関数）
    */
    private function timeToMin($time)
    {
        $t_array = explode(':', $time);
        $hour = $t_array[0] * 60;
        $second = round($t_array[2] / 60, 2);
        $mins = $hour + $t_array[1] + $second;
        return $mins;
    }

    /*
        予約の登録
    */
    public function store(Request $request)
    {
        $user_id = Auth::user()->id;
        $shop_id = $request->shop_id;
        $number = $request->number_of_people;
        $start_time = new DateTime( $request->date . ' ' . $request->start_time);
        $end_time = clone $start_time;
        $minutes = $this->timeToMin($request->time_per_reservation);
        $end_time->modify('+'.$minutes.' minutes');
        
        //空きテーブルの検出
        $tables = Table::select()->ShopIdSearch($shop_id)->SeatNumLargerOrEqualSearch($number)->get();
        $mindiff_table_id = null;
        $mindiff_seat_num = PHP_INT_MAX;
        foreach( $tables as $table ) {
            $reservations = $table->reservations()->EndsAfterSearch($start_time)->StartsBeforeSearch($end_time)->get();
            if ( empty($reservations->modelKeys()) ) {
                $tmpdiff_seat_num = $table->seat_num - $number;
                if ( $mindiff_seat_num > $tmpdiff_seat_num ) {
                    $mindiff_seat_num = $tmpdiff_seat_num;
                    $mindiff_table_id = $table->id;
                }
            }
        }

        //予約テーブルに追加
        $is_succeeded = false;
        if ( !empty($mindiff_table_id) ) {
            $new_reservation = Reservation::create([
                'user_id' => $user_id,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'number_of_people' => $number
            ]);
            $new_reservation->tables()->sync([$mindiff_table_id]);
            $is_succeeded = true;
        }

        return view('done', [ 'shop_id'=>$shop_id , 'is_succeeded'=>$is_succeeded]);
    }

}
