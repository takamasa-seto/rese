<?php

namespace App\Http\Controllers;

use Auth;
use Mail;
use DateTime;
use Illuminate\Http\Request;
use App\Http\Requests\AddAdminRequest;
use App\Models\Admin;
use App\Models\Shop;
use App\Models\User;
use App\Models\Reservation;
use App\Mail\SendAnnouncementMail;
use App\Mail\SendRemindMail;
use App\Http\Traits\Content;

class AdminController extends Controller
{
    use Content;

    /* 管理者の一覧表示 */
    public function index()
    {
        if (!$this->isAdmin(Auth::user()->role)) return redirect('admin/login');

        //店舗一覧
        $shop_list = array();
        $shops = Shop::all();
        foreach( $shops as $shop) {
            $shop_list[$shop->name] = $shop->id;
        }
        
        //管理者リストの作成
        $admins = Admin::all();
        $admin_list = array();
        foreach( $admins as $admin ) {
            $shops = $admin->shops()->get();
            $tmp_shop_list = array();
            foreach( $shops as $shop ) {
                $tmp_shop_list[] = [
                    'shop_id'=>$shop->id,
                    'shop_name'=>$shop->name
                ];
            }
            $admin_list[] = [
                'id'=>$admin->id,
                'name'=>$admin->name,
                'email'=>$admin->email,
                'role'=>$admin->role,
                'shops'=>$tmp_shop_list
            ];
        }
        
        return view('admin.admin_list', compact('admin_list', 'shop_list'));
    }
    
    /* 管理者の登録 */
    public function store(AddAdminRequest $request)
    {
        if (!$this->isAdmin(Auth::user()->role)) return redirect('admin/login');

        $message = '';
        // Emailが登録ずみか否かで新規作成か更新かを切り替える
        $admin = Admin::select()->EmailSearch($request->email)->get()->toArray();
        if ( empty($admin) ) {
            //新規作成
            $admin = Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'password' => 'xxxxxxxx'
            ]);
            if ( 1 == $request->role ) {
                //店舗責任者の場合は店舗を登録
                $admin->shops()->sync([$request->shop]);
            }
            $message = '新規登録を行いました。';
        } else {
            //登録情報の更新
            $admin = Admin::find($admin[0]['id']);
            $admin->update([
                'role' => $request->role
            ]);
            if ( 1 == $request->role ) {
                //店舗責任者の場合は店舗を登録
                $admin->shops()->syncWithoutDetaching([$request->shop]);
            } else {
                //管理者だった場合、店舗を削除
                $admin->shops()->sync([]);
            }
            $message = '登録情報を更新しました。';
        }
        return redirect('/admin/index')->with('message', $message);
    }

    /* 管理者の削除 */
    public function destroy(Request $request)
    {
        if (!$this->isAdmin(Auth::user()->role)) return redirect('admin/login');

        $admin_id = $request->admin_id;
        $admin = Admin::find($admin_id);
        $admin->shops()->detach();  //中間テーブルから関連レコードを削除
        $admin->delete();

        $message = '管理者を削除しました。';
        return redirect('/admin/index')->with('message', $message);
 
    }

    /* お知らせメール作成画面の表示 */
    public function makeAnnouncement()
    {
        return view('/admin/make_announcement');
    }

    /* お知らせメール送信 */
    public function send(Request $request)
    {
        $users = User::all();
        Mail::to($users)->send(new SendAnnouncementMail($request->title, $request->content));

        return redirect('/admin/make_announcement')->with('message', "メールを送信しました。");
    }

    /* リマインドメール送信(スケジューラから呼ばれる) */
    public function reminder()
    {
        //同じ日付の予約を検索
        $t1 = new DateTime();
        $t2 = new DateTime($t1->format('Y-m-d').' 23:59:59');
        $reservations = Reservation::select()->EndsAfterSearch($t1)->StartsBeforeSearch($t2)->get();

        foreach($reservations as $reservation) {
            $shop_id = $reservation->tables()->first()->shop_id;
            $shop = Shop::find($shop_id);
            $user = User::find($reservation->user_id);
            Mail::to($user)->send(new SendRemindMail($reservation, $user, $shop));
        }
    }
}
