<?php

namespace App\Http\Controllers;

use Auth;
use Mail;
use DateTime;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Feedback;
use App\Mail\SendFeedbackMail;

class FeedbackController extends Controller
{
    /*
        評価入力画面の表示
    */
    public function create(Request $request, $reservation_id)
    {
        $reservation = Reservation::find($reservation_id);
        if (empty($reservation)) return redirect('/my_page');
        
        $shop_id = $reservation->tables()->first()->shop_id;
        $shop = Shop::find($shop_id);
        $user = User::find($reservation->user_id);
        if ($user->id != Auth::user()->id) return redirect('/my_page');
        
        return view('feedback', compact('reservation', 'shop', 'user'));
    }

    /*
        評価の登録(登録済みの場合は更新)
    */
    public function store(Request $request)
    {
        $reservation = Reservation::find($request->reservation_id);
        if (empty($reservation)) return redirect('/my_page');

        $feedback = $reservation->feedback();
        if (!$feedback->exists()) {
            $table = [
                'reservation_id' => $request->reservation_id,
                'score' => $request->score,
                'comment' => $request->comment
            ];
            $feedback = Feedback::create($table);
        } else {
            $table = [
                'score' => $request->score,
                'comment' => $request->comment        
            ];
            $feedback->update($table);
        }

        return view('thanks');
    }

    /* アンケートのお願いメール送信(スケジューラから呼ばれる) */
    public function feedbackRequest()
    {
        //同じ日付の予約を検索
        $t2 = new DateTime();
        $t1 = new DateTime($t2->format('Y-m-d').' 00:00:00');
        $reservations = Reservation::select()->EndsAfterSearch($t1)->StartsBeforeSearch($t2)->get();

        foreach($reservations as $reservation) {
            $shop_id = $reservation->tables()->first()->shop_id;
            $shop = Shop::find($shop_id);
            $user = User::find($reservation->user_id);
            Mail::to($user)->send(new SendFeedbackMail($reservation, $user, $shop));
        }
    }

}
