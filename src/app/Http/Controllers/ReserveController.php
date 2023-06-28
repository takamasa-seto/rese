<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Shop;
use App\Models\Table;
use App\Http\Traits\Content;

use Auth;
use DateTime;

class ReserveController extends Controller
{
    use Content;
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

    /*
        予約の削除
    */
    public function destroy(Request $request)
    {
        $reservation_id = $request->reservation_id;
        $reservation = Reservation::find($reservation_id);
        $reservation->tables()->detach();  //中間テーブルから関連レコードを削除
        $reservation->delete();

        return redirect('/my_page');
    }

    /*
        予約キャンセル確認画面の表示
    */
    public function showCancel(Request $request)
    {
        $reservation_id = $request->reservation_id;
        $reservation = Reservation::find($reservation_id);
        $shop_id = $reservation->tables()->first()->shop_id;
        $shop_name = Shop::find($shop_id)->name;
        $dtime = new DateTime($reservation->start_time);
        $date = $dtime->format('Y-m-d');
        $start_time = $dtime->format('H:i');
        $number_of_people = $reservation->number_of_people;

        return view('cancel', compact('reservation_id', 'shop_name', 'date', 'start_time', 'number_of_people'));
    }
}
