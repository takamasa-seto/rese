<?php

namespace App\Http\Controllers;

use Auth;
use DateTime;
use Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Admin;
use App\Models\Shop;
use App\Models\Favorite;
use App\Models\Table;
use App\Models\Review;
use App\Http\Requests\UpdateShopRequest;
use App\Http\Requests\AddShopRequest;
use App\Http\Traits\Content;
use App\Consts\SortOptConst;

class ShopController extends Controller
{
    use Content;
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

        /* ソートオプションの取得 */
        $sort_option = session()->has('sort_option') ? session('sort_option') : SortOptConst::RANDOM;
        if( $request->has('sort_option') ) $sort_option = $request->sort_option;
        session(['sort_option' => $sort_option]);
        
        /* region検索キーの取得 */
        $search_region = session()->has('search_region') ? session('search_region') : $all_area;
        if( $request->has('search_region') ) $search_region = $request->search_region;
        session(['search_region' => $search_region]);
        if ($search_region == $all_area) $search_region = NULL;
        
        /* genre検索キーの取得 */
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
            ->NameSearch($search_name);

        if (SortOptConst::RANDOM == $sort_option) {
            $shops = $shops->inRandomOrder();
            $shops = $shops->get();
        } elseif (SortOptConst::DESCENDING == $sort_option or SortOptConst::ASCENDING == $sort_option) {
            $shops = $shops->get();
            $shops = Arr::sort($shops, function($value) {
                $reviews = $value->reviews()->get();
                return $reviews->isEmpty() ? 0 : $reviews->avg('star');
            });  //Arr::sortはコレクションもソートできる.戻り値は配列
            if (SortOptConst::DESCENDING == $sort_option) $shops = array_reverse($shops);
        }
        /* viewに渡す用にデータを加工 */
        foreach ($shops as $shop) {
            $shop['image_url'] = Storage::url($shop['image_url']);
            $shop['favorite'] = in_array($shop['id'], $favorites);
        }

        return view('shop_index', compact('shops', 'regions', 'genres'));
    }

    /*
        店の予約時間リストを取得する（プライベート関数）
    */
    private function getTimeArray($date, $operation_pattern, $time_per_reservation)
    {
        $time_array = array();
        $explanation = "";
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
        店の詳細表示
    */
    public function detail(Request $request, $shop_id)
    {
        $shop = Shop::find($shop_id);
        $shop['image_url'] = Storage::url($shop['image_url']);
        
        $tomorrow = now()->addDay()->format('Y-m-d');
        if( $request->has('date') ) {
            $reserve_date = $request->date;
        } else {
            $reserve_date = session()->has('date') ? session('date') : $tomorrow;
        }
        session(['date' => $reserve_date]);
        
        [$time_explanation, $time_array] = $this->getTimeArray($reserve_date, $shop->operation_pattern, $shop->time_per_reservation);
        $num_array = $this->getNumArray($shop['id']);

        $my_review = null;
        if( Auth::check() ){
            $tmp_review = Review::select()->UserSearch(Auth::id())->ShopSearch($shop_id)->get();
            $my_review = $tmp_review->isEmpty() ? null : $tmp_review->toArray()[0];
            if ( !is_null($my_review) ) {
                $my_review['image_url'] = empty($my_review['image_url']) ? null : Storage::url($my_review['image_url']);
            }
        }
        return view('shop_detail', compact('shop', 'tomorrow', 'reserve_date', 'time_explanation', 'time_array', 'num_array', 'my_review'));
    }

    /*
        ストレージに画像を保存する(プライベート)
    */
    private function myStoreImage($img_file)
    {
        $path = '';
        if(isset($img_file)) {
            $filename = $img_file->getClientOriginalName();
            $path = 'rese\image\\'.$filename;
            if('local' == env('FILESYSTEM_DRIVER')) {
                Storage::putFileAs('', $img_file, 'public\\'.$path);
            } else {
                Storage::putFileAs('', $img_file, $path);
            }
            
        }
        return $path;
    }

    /*
        店舗情報の更新
    */
    public function update(UpdateShopRequest $request)
    {
        if (!$this->isShopStaff(Auth::user()->role)) return redirect('admin/login');

        //ストレージに画像を登録
        $image = $request->file('image_file');
        $path = $this->myStoreImage($image);

        //更新情報を作成
        $update_info = [
            'name' => $request->name,
            'region' => $request->region,
            'genre' => $request->genre,
            'description' => $request->description
        ];
        if(!empty($path)) $update_info['image_url'] = $path;

        //更新
        $shop = Shop::find($request->id);
        $shop->update($update_info);

        $message = '店舗情報を更新しました。';   
        return redirect('/admin/edit') ->with('message', $message);
    }


    /*
        店舗情報の追加
    */
    public function store(AddShopRequest $request)
    {
        if (!$this->isShopStaff(Auth::user()->role)) return redirect('admin/login');
        
        //ストレージに画像を登録
        $image = $request->file('image_file');
        $path = $this->myStoreImage($image);

        //店舗情報を作成
        $new_info = [
            'name' => $request->name,
            'region' => $request->region,
            'genre' => $request->genre,
            'operation_pattern' => $request->operation_pattern,
            'time_per_reservation' => $request->time_per_reservation,
            'description' => $request->description
        ];
        $new_info['image_url'] = empty($path)? null: $path;

        //追加
        $shop = Shop::create($new_info);

        // テーブル（座席）を追加
        if ( isset($request->tables) ) {
            foreach( $request->tables as $key => $value ) {
                Table::create([
                    'shop_id' => $shop->id,
                    'name' => $key,
                    'seat_num' => $value
                ]);
            }
        }

        // 自身を管理者に登録
        $admin = Admin::find(Auth::user()->id);
        if ( 1 == $admin->role ) {
            //店舗責任者の場合は店舗を登録
            $admin->shops()->syncWithoutDetaching([$shop->id]);
        }

        $message = '店舗を新規登録しました。';   
        return redirect('/admin/new_shop') ->with('message', $message);
    }

}
