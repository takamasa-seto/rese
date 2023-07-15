<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendFeedbackMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($reservation, $user, $shop)
    {
        $this->reservation = $reservation;
        $this->user = $user;
        $this->shop = $shop;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.feedback')
                    ->subject("アンケートご協力のお願い。")
                    ->with([
                        'reservation' => $this->reservation,
                        'user' => $this->user,
                        'shop' => $this->shop
                    ]);
    }
}
