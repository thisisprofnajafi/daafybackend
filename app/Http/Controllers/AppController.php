<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Nnjeim\World\World;


class AppController extends Controller
{

    public function getCountries()
    {

        $action = World::countries();

        if ($action->success) {

            $countries = $action->data;


            return RespondHandler::respond(['status'=>true,'countries' => $countries ], 200);
        } else {
            return RespondHandler::respond(['status'=>false], 200);
        }
    }

    public function getStates($code)
    {


        $action = World::states([
            'filters' => [
                'country_id' => $code,
            ],
        ]);


        $states = $action->data;
        return RespondHandler::respond(['status'=>true,'states' => $states ], 200);


    }



    public function getCities($code)
    {


        $action = World::cities([
            'filters' => [
                'state_id' => $code,
            ],
        ]);


            $cities = $action->data;
            return RespondHandler::respond(['status'=>true,'cities' => $cities ], 200);


    }



    public function checkUsername($username){

        $user = User::all()->where('username',$username)->first();
        if($user){
            return RespondHandler::respond(['taken' => true], 200);
        }else{
            return RespondHandler::respond(['taken' => false], 200);
        }
    }
}
