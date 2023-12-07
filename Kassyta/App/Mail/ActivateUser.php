//php artisan make:mail ActivateUser

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ActivateUser extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $signedUrl;

    public function __construct($user, $signedUrl)
    {
        $this->user = $user;
        $this->signedUrl = $signedUrl;
    }

    public function build()
    {
        return $this->view('emails.activate_user');
    }
}
