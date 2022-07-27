<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Str;


class PaymentController extends Controller
{
    
    public function setPayment(Request $request){

        $user = auth()->user();

        $payment = new Payment();
        $payment->type = $request->type;
        $payment->price = $request->price;
        $payment->token = Str::random(80);
        $user->payments()->save($payment);

        return RespondHandler::respond(['status'=>true,'token' => $payment->token], 200);

    }
}
