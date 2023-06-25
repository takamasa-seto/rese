<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shop;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    /*
        お気に入り登録状態を反転させる関数
        お気に入りに既に登録されている場合はお気に入りから削除、そうでない場合はお気に入り登録する
    */
    public function flip(Request $request)
    {
        if( !$request->has('user_id') ) return back();
        if( !$request->has('shop_id') ) return back();

        $favorite = Favorite::select()
            ->UserSearch($request->user_id)
            ->ShopSearch($request->shop_id)
            ->first();
        
        if( is_null($favorite) ) {
            $user = User::find($request->user_id);
            $shop = Shop::find($request->shop_id);
            if( !is_null($user) and !is_null($shop)) {
                Favorite::create(['user_id' => $user->id, 'shop_id' => $shop->id]);
            }
        } else {
            Favorite::find($favorite->id)->delete();
        }
        
        return back();
    }    
}
