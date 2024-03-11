//php artisan make:mail AgradecimientoVerificacion

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AgradecimientoVerificacion extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this->view('emails.agradecimiento');
    }
}