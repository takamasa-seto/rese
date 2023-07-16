<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Shop;
use App\Models\Favorite;
use App\Models\Table;
use App\Models\Reservation;
use DateTime;

class MyPageController extends Controller
{
    /*
        予約している店舗情報を取得する(プライベート関数)
    */
    private function getRreservedShops($user_id)
    {
        $reservations = Reservation::select()->UserIdSearch($user_id)->EndsAfterSearch(new DateTime())->get();
        $reserved_shops = array();
        $reservation_num = 1;
        foreach( $reservations as $reservation ) {
            $shop_id = $reservation->tables()->first()->shop_id;
            $shop_name = Shop::find($shop_id)->name;
            $dtime = new DateTime($reservation->start_time);
            $date = $dtime->format('Y-m-d');
            $time = $dtime->format('H:i');
            $reserve_info = array(
                'reservation_num' => $reservation_num,
                'id' => $reservation->id,
                'shop_name'=>$shop_name,
                'date'=>$date,
                'start_time'=>$time,
                'number_of_people'=>$reservation->number_of_people
            );
            $reserved_shops[] = $reserve_info;
            $reservation_num ++;
        }

        return $reserved_shops;
    }

    /*
        お気に入り店舗情報を取得する(プライベート関数)
    */
    private function getFavoriteShops($user_id)
    {
        $favorites = Favorite::select()->UserSearch($user_id)->get();
        $favorite_shops = array();
        foreach( $favorites as $favorite ) {
            $shop_id = $favorite->shop_id;
            $shop = Shop::find($shop_id);
            $shop_info = array(
                'id' => $shop->id,
                'name' => $shop->name,
                'region' => $shop->region,
                'genre' => $shop->genre,
                'image_url' => Storage::url($shop->image_url),
                'favorite' => true
            );
            $favorite_shops[] = $shop_info;
        }

        return $favorite_shops;
    }

    /*
        マイページを表示する
    */
    public function create()
    {
        $user_id = Auth::user()->id;
        $reservations = $this->getRreservedShops($user_id);
        $favorites = $this->getFavoriteShops($user_id);

        return view('my_page', compact('reservations', 'favorites'));
    }

    /*
        QRコードを表示する
    */
    public function showQrCode(Request $request)
    {
        $reservation_id = $request->reservation_id;
        return view('qr_code', compact('reservation_id'));
    }
}
