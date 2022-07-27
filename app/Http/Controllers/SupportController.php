<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use App\Mail\ContactSupportMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{

    public function help(Request $request){

        $user = auth()->user();

        Mail::to('profnajafi@yahoo.com')->send(new ContactSupportMail($request->message , $user));
        Mail::to($user->email)->send(new ContactMail($request->message , $user));

        Return RespondHandler::respond(['status'=>true],200);
    }
}
