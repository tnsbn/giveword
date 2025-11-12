<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\MailablePassword;
use App\Models\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */
    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function sendResetLinkEmail(Request $request, User $user)
    {
        $this->validateEmail($request);
        $user = $user->getByEmail($request['email'] ?? '');
        $strError = "We can't find that email in our system.";
        $strSuccess = "We have emailed you a password reset link. ";
        if (empty($user)) {
            return $this->sendResetLinkFailedResponse($request, $strError);
        }
        $message = (new MailablePassword($user))
            ->onConnection('database')
            ->onQueue('emails');
        $queueId = Mail::to($request['email'])
            ->queue($message);

        return is_int($queueId) && $queueId > 0
            ? $this->sendResetLinkResponse($request, $strSuccess)
            : $this->sendResetLinkFailedResponse($request, $strError);
    }

    public function runAir(Request $request, User $user)
    {
        $email = 'harry@suw.com';
        $user = $user->getByEmail($email);
        $strError = "We can't find that email in our system.";
        $strSuccess = "We have emailed you a password reset link. ";
        if (empty($user)) {
            return $this->sendResetLinkFailedResponse($request, $strError);
        }
        $message = (new MailablePassword($user))
            ->onConnection('database')
            ->onQueue('emails');
        $queueId = Mail::to($email)
            ->queue($message);
        return $queueId;
    }
}
