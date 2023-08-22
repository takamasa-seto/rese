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
            if ( $this->isAdmin(Auth::user()->role) ) $account_check = true;
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
}
