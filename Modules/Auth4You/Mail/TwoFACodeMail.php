<?php

namespace Modules\Auth4You\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TwoFACodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject('Tu código 2FA')
            ->view('auth4you.emails.two_fa_code')
            ->with(['code' => $this->code]);
    }
}
