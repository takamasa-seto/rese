<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddReviewRequest;
use Illuminate\Support\Facades\Storage;
use Auth;
use App\Models\Shop;
use App\Models\Favorite;
use App\Models\Review;
use App\Http\Traits\Content;

class ReviewController extends Controller
{
    use Content;

    /*
        ストレージに画像を保存する(プライベート)
    */
    private function myStoreImage($img_file)
    {
        $path = '';
        if(isset($img_file)) {
            $filename = $img_file->getClientOriginalName();
            $path = 'rese\review\\'.$filename;
            if('local' == env('FILESYSTEM_DRIVER')) {
                Storage::putFileAs('', $img_file, 'public\\'.$path);
            } else {
                Storage::putFileAs('', $img_file, $path);
            }
            
        }

        return $path;
    }

    /*
        レビューの登録画面表示
    */
    public function create(Request $request, $shop_id)
    {
        if ( !Auth::check() ) return redirect('login');

        $shop = Shop::find($shop_id);
        $shop['image_url'] = Storage::url($shop['image_url']);
        $tmp_favorites = Favorite::select()->UserSearch(Auth::id())->ShopSearch($shop_id)->get();
        $shop['favorite'] = !$tmp_favorites->isEmpty();

        return view('review_add', compact('shop'));
    }

    /*
        レビューの登録
    */
    public function store(AddReviewRequest $request)
    {
        if ( !Auth::check() ) return redirect('login');
        
        //ストレージに画像を登録
        $image = $request->file('image_file');
        $path = $this->myStoreImage($image);

        $table = [
            'user_id' => $request->user_id,
            'shop_id' => $request->shop_id,
            'star' => $request->star,
            'comment' => $request->comment,
        ];
        $table['image_url'] = empty($path)? null: $path;

        //追加
        $review = Review::create($table);

        return redirect('/detail/'.$request->shop_id);
    }

    /*
        レビューの削除
    */
    public function destroy(Request $request)
    {
        //投稿したユーザか、管理者のみが削除可能
        $account_check = false;
        if ( Auth::guard('admin')->check() ) {
            if ( $this->isAdmin(Auth::guard('admin')->user()->role) ) $account_check = true;
        }
        elseif ( Auth::check() ) {
            if ( Auth::user()->id == $request->user_id ) $account_check = true;
        }

        //削除
        if ( $account_check ) {
            $review = Review::select()->UserSearch($request->user_id)->ShopSearch($request->shop_id);
            $review->delete();
        }

        return redirect()->back();
    }

    /*
        レビューの編集画面表示
    */
    public function edit(Request $request, $shop_id)
    {
        if ( !Auth::check() ) return redirect('login');

        $shop = Shop::find($shop_id);
        $shop['image_url'] = Storage::url($shop['image_url']);
        $tmp_favorites = Favorite::select()->UserSearch(Auth::id())->ShopSearch($shop_id)->get();
        $shop['favorite'] = !$tmp_favorites->isEmpty();

        $review = Review::select()->UserSearch(Auth::id())->ShopSearch($shop_id)->first();
        if ( is_null($review) ) return redirect()->back(); 
        $review['image_url'] = empty($review['image_url']) ? null : Storage::url($review['image_url']);

        return view('review_edit', compact('shop', 'review'));
    }

    /*
        レビューの更新
    */
    public function update(AddReviewRequest $request)
    {
        if ( !Auth::check() ) return redirect('login');
        
        //ストレージに画像を登録
        $image = $request->file('image_file');
        $path = $this->myStoreImage($image);

        $table = [
            'user_id' => $request->user_id,
            'shop_id' => $request->shop_id,
            'star' => $request->star,
            'comment' => $request->comment,
        ];
        $table['image_url'] = empty($path)? null: $path;

        //更新
        $review = Review::select()->UserSearch($request->user_id)->ShopSearch($request->shop_id);
        switch ($request->img_edit_mode) {
            case 0:
                //変更しない
                $tmp_review = $review->first();
                $table['image_url'] = $tmp_review['image_url'];
                break;
            case 1:
                //削除
                $table['image_url'] = null;
                break;
        }
        $review->update($table);

        return redirect('/detail/'.$request->shop_id);
    }

    /*
        店舗ごとの口コミ表示
    */
    public function shopIndex(Request $request, $shop_id)
    {
        $shop = Shop::find($shop_id);
        $shop['image_url'] = Storage::url($shop['image_url']);
        $reviews = Review::select()->ShopSearch($shop_id)->get();
        foreach($reviews as $review) {
            $review['image_url'] = empty($review['image_url']) ? null : Storage::url($review['image_url']);
        }

        return view('review_shop_index', compact('shop', 'reviews'));
    }

    /*
        管理者用の口コミ閲覧画面
    */
    public function adminIndex(Request $request)
    {
        if ( !Auth::guard('admin')->check() ) return redirect('admin/login'); 
        if (!$this->isAdmin(Auth::guard('admin')->user()->role)) return redirect('admin/login');

        $all_shops = 'All shops';     // 全店舗のフィルタに表示する文字列

        /* ドロップダウンリストに表示する文字列の生成 */
        $shops = Shop::all();
        $shop_indexes = array($all_shops);
        foreach ($shops as $shop) {
            $shop_indexes[] = $shop['name'];
        }
        
        //セッション情報の取得
        $search_shop = session()->has('search_shop') ? session('search_shop') : $all_shops;
        if( $request->has('search_shop') ) $search_shop = $request->search_shop;
        session(['search_shop' => $search_shop]);
        if ($search_shop == $all_shops) $search_shop = NULL;
 
        //検索
        $shops = Shop::select()->NameSearch($search_shop)->get();
        $reviews = [];
        foreach ($shops as $shop) {
            $tmp_reviews = Review::select()->ShopSearch($shop->id)->get();
            foreach ($tmp_reviews as $tmp_review) {
                $tmp_array = [
                    'shop_id' => $tmp_review->shop->id,
                    'shop_name' => $tmp_review->shop->name,
                    'user_id' => $tmp_review->user->id,
                    'user_name' => $tmp_review->user->name,
                    'star' => $tmp_review->star,
                    'comment' => $tmp_review->comment,
                    'image_url' => empty($tmp_review->image_url) ? null : Storage::url($tmp_review->image_url)
                ];
                $reviews[] = $tmp_array;
            }
        }
        
        return view('/admin/review_manager', compact('shop_indexes', 'reviews'));        
    }
}
