<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Table;
use App\Models\Reservation;
use App\Models\Admin;
use App\Models\User;
use App\Http\Traits\Content;

class MaintenanceController extends Controller
{
    use Content;

    public function showReservations(Request $request)
    {
        if (!$this->isShopStaff(Auth::user()->role)) return redirect('admin/login');

        $admin_id = Auth::user()->id;
        //店舗一覧
        $shop_list = array();
        $shops = Admin::find($admin_id)->shops()->get();
        foreach( $shops as $shop) {
            $shop_list[$shop->name] = $shop->id;
        }

        $shop_index = $shops[0]->shop_id;
        if(isset($request->shop_index)) {
            $shop_index = $request->shop_index;
        } else if(session()->has('shop_index')) {
            $shop_index = session('shop_index');
        }

        $tables = Table::select()->ShopIdSearch($shop_index)->get();
        $reservation_ids = array();
        foreach($tables as $table){
            $tmp_reservation_ids = $table->reservations()->pluck('reservation_id')->toArray();
            $reservation_ids = array_merge($reservation_ids, $tmp_reservation_ids);
        }
        $reservations = array();
        foreach($reservation_ids as $reservation_id) {
            $reserve = Reservation::find($reservation_id);
            $user = User::find($reserve->user_id);
            $tables = $reserve->tables()->pluck('name');
            $reservations[] = [
                'id' => $reserve->id,
                'user_id' => $reserve->user_id,
                'start_time' => $reserve->start_time,
                'end_time' => $reserve->end_time,
                'number_of_people' => $reserve->number_of_people,
                'user_name' => $user->name,
                'tables' => $tables
            ];
        }
        session(['shop_index' => $shop_index]);

        return view('admin.reservation_list', compact('shop_list', 'reservations'));
    }
}
