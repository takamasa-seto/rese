<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Reservation;
use App\Models\Shop;
use App\Models\Table;
use App\Http\Traits\Content;
use App\Http\Requests\StoreReservationRequest;

use Auth;
use DateTime;

class ReserveController extends Controller
{
    use Content;
    /*
        予約の登録
    */
    public function store(StoreReservationRequest $request)
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

        return view('done', ['shop_id'=>$shop_id, 'is_succeeded'=>$is_succeeded, 'is_update'=>false]);
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

    /*
        店の予約時間リストを取得する（プライベート関数）
    */
    private function getTimeArray($date, $operation_pattern, $time_per_reservation)
    {
        $time_array = array();
        $tmp_date = new DateTime($date);
        $w = (int)date_format( $tmp_date, 'w');
        $minus_min = $this->timeToMin($time_per_reservation);
        switch( $operation_pattern ) {
            case 1:
                $explanation = "営業時間11:00～22:00、月曜定休";
                if ( 1 != $w ) {
                    $start_time = new DateTime($tmp_date->format('Y-m-d'.' 11:00:00'));
                    $end_time = new DateTime($tmp_date->format('Y-m-d'.' 22:00:00'));
                    $end_time->modify('-'.$minus_min.' minutes');
                    for ($tmp_time = $start_time; $tmp_time <= $end_time; $tmp_time->modify('+30 minutes')) {
                        $time_array[] = $tmp_time->format('H:i');
                    }
                }
                break;
            case 2:
                $explanation = "営業時間17:00～23:00、年中無休";
                $start_time = new DateTime($tmp_date->format('Y-m-d'.' 17:00:00'));
                $end_time = new DateTime($tmp_date->format('Y-m-d'.' 23:00:00'));
                $end_time->modify('-'.$minus_min.' minutes');
                for ($tmp_time = $start_time; $tmp_time <= $end_time; $tmp_time->modify('+30 minutes')) {
                    $time_array[] = $tmp_time->format('H:i');
                }
                break;
        }
        return array($explanation, $time_array);
    }

    /*
        店の予約人数リストを取得する（プライベート関数）
    */
    private function getNumArray($shop_id)
    {
        $tables = Table::select()->ShopIdSearch($shop_id)->get();
        $max_num = 0;
        foreach( $tables as $table ) {
            if( $max_num < $table->seat_num ) {
                $max_num = $table->seat_num;
            }
        }
        
        $num_array = array();
        for( $i = 1; $i <= $max_num; $i++ ) {
            $num_array[] = $i;
        }

        return $num_array;
    }

    /*
        予約の変更画面表示
    */
    public function edit(Request $request)
    {
        $reservation_id = $request->has('reservation_id') ? $request->reservation_id: session('reservation_id');
        session(['reservation_id' => $reservation_id]);

        $reservation = Reservation::find($reservation_id);
        $shop_id = $reservation->tables()->first()->shop_id;
        $shop = Shop::find($shop_id);
        $shop['image_url'] = Storage::url($shop['image_url']);
        
        $today = now()->format('Y-m-d');
        if( $request->has('date') ) {
            $reserve_date = $request->date;
        } else {
            $reserve_date = session()->has('date') ? session('date') : $today;
        }
        session(['date' => $reserve_date]);
        
        [$time_explanation, $time_array] = $this->getTimeArray($reserve_date, $shop->operation_pattern, $shop->time_per_reservation);
        $num_array = $this->getNumArray($shop['id']);

        return view('reservation_change', compact('shop', 'today', 'reservation_id', 'reserve_date', 'time_explanation', 'time_array', 'num_array'));

    }

    /*
        予約の変更
    */
    public function update(Request $request)
    {
        $reservation_id = $request->reservation_id;
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
            $reservation = Reservation::find($reservation_id);
            $reservation->update([
                'start_time' => $start_time,
                'end_time' => $end_time,
                'number_of_people' => $number
            ]);
            $reservation->tables()->sync([$mindiff_table_id]);
            $is_succeeded = true;
        }

        return view('done', ['shop_id'=>$shop_id, 'is_succeeded'=>$is_succeeded, 'is_update'=>true]);

    }

}
