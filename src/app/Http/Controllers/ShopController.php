<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Shop;
use App\Models\Favorite;
use App\Models\Table;
use DateTime;

class ShopController extends Controller
{
    /*
        店の一覧表示
    */
    public function index(Request $request)
    {
        $all_area = 'All area';     // 全地域のフィルタに表示する文字列
        $all_genre = 'All genre';     // 全ジャンルのフィルタに表示する文字列

        /* ドロップダウンリストに表示する文字列の生成 */
        $shops = Shop::all();
        $regions = array($all_area);
        $genres = array($all_genre);
        foreach ($shops as $shop) {
            $regions[] = $shop['region'];
            $genres[] = $shop['genre'];
        }
        $regions = array_unique($regions);
        $genres = array_unique($genres);

        /* お気に入り店リストを生成 */
        $favorites = array();
        if ( Auth::check() ) {
            $tmp_favorites = Favorite::select()->UserSearch(Auth::id())->get();
            foreach ($tmp_favorites as $tmp_favorite) {
                $favorites[] = $tmp_favorite['shop_id'];
            }
        }

        /* 検索キーの取得 */
        $search_region = session()->has('search_region') ? session('search_region') : $all_area;
        if( $request->has('search_region') ) $search_region = $request->search_region;
        session(['search_region' => $search_region]);
        if ($search_region == $all_area) $search_region = NULL;
        
        $search_genre = session()->has('search_genre') ? session('search_genre') : $all_genre;
        if( $request->has('search_genre') ) $search_genre = $request->search_genre;
        session(['search_genre' => $search_genre]);
        if ($search_genre == $all_genre) $search_genre = NULL;
 
        $search_name = session()->has('search_name') ? session('search_name') : NULL;
        if( $request->has('search_name') ) {
            $search_name = $request->search_name;
            session(['search_name' => $search_name]);
        }

        /* 検索実行　*/
        $shops = Shop::select()
            ->RegionSearch($search_region)
            ->GenreSearch($search_genre)
            ->NameSearch($search_name)
            ->get();
        
        /* viewに渡す用にデータを加工 */
        foreach ($shops as $shop) {
            $shop['image_url'] = Storage::url($shop['image_url']);
            $shop['favorite'] = in_array($shop['id'], $favorites);
        }

        return view('shop_index', compact('shops', 'regions', 'genres'));
    }

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
                $start_time = new DateTime($tmp_date->format('Y-m-d'.' 17:00:00'));
                $end_time = new DateTime($tmp_date->format('Y-m-d'.' 23:00:00'));
                $end_time->modify('-'.$minus_min.' minutes');
                for ($tmp_time = $start_time; $tmp_time <= $end_time; $tmp_time->modify('+30 minutes')) {
                    $time_array[] = $tmp_time->format('H:i');
                }
                break;
        }
        return $time_array;
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
        店の詳細表示
    */
    public function detail(Request $request, $shop_id)
    {
        $shop = Shop::find($shop_id);
        $shop['image_url'] = Storage::url($shop['image_url']);
        
        $today = now()->format('Y-m-d');
        if( $request->has('date') ) {
            $reserve_date = $request->date;
        } else {
            $reserve_date = session()->has('date') ? session('date') : $today;
        }
        session(['date' => $reserve_date]);
        
        $time_array = $this->getTimeArray($reserve_date, $shop->operation_pattern, $shop->time_per_reservation);
        $num_array = $this->getNumArray($shop['id']);

        return view('shop_detail', compact('shop', 'today', 'reserve_date', 'time_array', 'num_array'));
    }
}
