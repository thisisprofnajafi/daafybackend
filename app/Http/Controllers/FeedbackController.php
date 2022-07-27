<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    //

    public function feedback(Request $request){

        $feedback = new Feedback();
        $feedback->feedback = $request->feedback;

        auth()->user()->feedbacks()->save($feedback);

        return RespondHandler::respond(['status'=>true],200);


    }

}
