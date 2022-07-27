<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Hash;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasVerifiedEmail()) {
            if ($user->verification_token_expire >= Carbon::now()) {
                if ($request->token == $user->verification_token) {

                    $user->markEmailAsVerified();
                    $user->verification_token = null;
                    $user->verification_token_expire = null;
                    $user->save();
                    return RespondHandler::respond(['status' => true], 200);

                } else {
                    return RespondHandler::respond(['status' => false, "msg" => "Wrong Credentials"], 200);
                }
            } else {
                RespondHandler::respond(['status' => false, 'msg' => 'Token Expired'], 200);
            }
        } else {
            return RespondHandler::respond(['status' => true, "msg" => "Email Already Verified."], 200);
        }
    }

    public function resend()
    {

        if (auth()->user()->hasVerifiedEmail()) {
            return response()->json(['status' => false, "msg" => "Email already verified."], 400);
        }

        auth()->user()->sentEmailVerification();

        return response()->json(['status' => true, "msg" => "New Email Verification Link Sent To Your Email"]);
    }
}
