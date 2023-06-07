<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Shop;
use App\Models\Favorite;

class ShopController extends Controller
{
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
}
