<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\URL;
// use Illuminate\Foundation\Auth\VerifiesEmails;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    // use VerifiesEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Iuser $users)
    {
        // $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
        $this->users = $users;
    }

    public function verify(Request $request, User $user)
    {
        // check if the url is a valid  signed url
        if (! URL::hasValidSignature($request)) {
           return response()->json(["errors"=>[
               "message" => "Invalid Verification link"
           ]], 422);
        }

        // check if the user has already verified account
        if ($user->hasVerifiedEmail()){
            return response()->json(["errors" => [
                "message" => "Email address already verified"
            ]], 422);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json(['message' => 'Email successfully verified'], 200);
    }

    public function resend(Request $request)
    {
        $this->validate($request, [
            'email' => ['email', 'required']
        ]);

        $user = $this->users->findWhereFirst('email', $request->email)->first();
        // $user = User::where('email', $request->email)->first();
        if (! $user) {
            return response()->json(["errors" => [
                "email" => "No user could be found with this eamil address"
            ]], 422);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(["errors"=>[
                "message"=>"Email address already verified"
            ]],422);
        }

        $user -> sendEmailVerificationNotification();

        return response()->json(['status' => "verification link resent"]);
    }
}
