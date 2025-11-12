<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Password;

class MailablePassword extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected User $user;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
//        $this->afterCommit();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Reset password of {$this->user->name} on SUW",
            tags: ['password'],
        );
    }

    /**
     * Get the message headers.
     */
    public function headers(): Headers
    {
        return new Headers(
            messageId: 'password-reset-message-id@suw.com',
            references: ['marketing@suw.com'],
            text: [
                'X-Custom-Header' => 'Suw Support',
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $minutes = intval(config('auth.passwords.' . config('auth.defaults.passwords') . '.expire'));
        $now1 = Date::now();
        $now2 = Date::now();
        $now2->addMinutes($minutes);
        $expire = date_diff($now1, $now2);
        $expStr = $expire->h > 0 ? $expire->h . ' hours' : '';
        $expStr .= $expire->i > 0 ? ' ' . $expire->i . ' minutes' : '';
        $token = Password::createToken($this->user);
        $url =  'https://giveaword.com/password/reset/' . $token . '?email=' . urlencode($this->user->email);
//        $url = url(route('password.reset', [
//            'token' => $token,
//            'email' => $this->user->email,
//        ], false));

        return new Content(
            html: 'auth.passwords.content',
            with: [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'expire' => $expStr,
                'token' => $token,
                'url' => $url,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
