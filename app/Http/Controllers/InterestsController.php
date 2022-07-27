<?php

namespace App\Http\Controllers;

use App\Models\Interests;
use App\Models\User;
use Illuminate\Http\Request;

class InterestsController extends Controller
{
    // Interests
    public function setInterests(Request $request)
    {
        $user = auth()->user();

        if($user->interests)
            $user->interests->delete();

            $interest = new Interests();

            $interest->age = json_encode($request->age);

            $interest->gender = $request->gender;

            $interest->height = json_encode($request->height);

            $interest->body_type = json_encode($request->body_type);

            $interest->drink = json_encode($request->drink);

            $interest->smoke = json_encode($request->smoke);

            $interest->employment_status = json_encode($request->employment_status);

            $interest->living_status = json_encode($request->living_status);

            $interest->seeking_for = json_encode($request->seeking_for);

            $interest->education_status = json_encode($request->education_status);

            $query  = $user->interests()->save($interest);

            if ($query) {
                RespondHandler::respond(['status' => true], 200);
            }else{
                RespondHandler::respond(['status'=>false],200);
            }RespondHandler::respond(['status'=>false],200);
    }


    public function getInterests(){

        $interests = auth()->user()->interests;

        return RespondHandler::respond(['status'=>true,'interests'=>$interests],200);

    }



}
