<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Mail\Attachment;

class ActivateUser extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The order instance.
     *
     * @var \App\Models\User
     */
    protected $user;
    protected $signedUrl;
    public $pathToImage = 'https://i1.sndcdn.com/artworks-zFF6phNX9B1m1zhJ-V4Zu7A-t500x500.jpg';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $signedUrl)
    {
        $this->user = $user;
        $this->signedUrl = $signedUrl;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            //from: new Address('tomorrowismyday23@gmail.com', 'GrrrAgent'),
            subject: 'Activate your account',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            /*view: 'view.name',
            html: 'emails.activate.user',
            text: 'emails.activate.user-text',
            html: 'welcome',
            text: 'welcome',
            */
            html: 'emails_activate',
            text: 'emails_activate-text',
            with: [
                'userName' => $this->user->name,
                'activateUrl' => $this->signedUrl,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [
            /*
            Attachment::fromStorageDisk('s3', '/path/to/file')
                ->as('name.pdf')
                ->withMime('application/pdf'),
            
            Attachment::fromPath('https://i1.sndcdn.com/artworks-zFF6phNX9B1m1zhJ-V4Zu7A-t500x500.jpg')
                    ->as('yeojin.jpg')
                    ->withMime('image/jpeg'),
                    */
        ];
    }
}
