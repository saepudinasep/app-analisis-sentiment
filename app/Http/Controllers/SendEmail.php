<?php

namespace App\Http\Controllers;

use App\Mail\SendingEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class SendEmail extends Controller
{

    public function index()
    {
        // return view('emails.sending-password');
        $email = 'warungcodingumi@gmail.com';
        // $user = User::where('email', $email)->first();
        // if ($user) {
        // $user->password = "";
        Mail::to($email)->send(new SendingEmail());

        // return redirect('/send-email');
        // }
    }

    public function store(Request $request)
    {
        $resetLink = url('/reset-password');
        $email = $request->email;
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->password = "";
            Mail::to($email)->send(new SendingEmail($user, $resetLink));

            return redirect('/send-email');
        }
    }
}
