<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SpecialController extends Controller
{
    //Special Match

    public static function getTodaySpecial()
    {

        $user = auth()->user();

        $special = $user->todaySpecial();

        $status = $special ? true : false;

        return ['status'=>$status , 'special'=>$special];


    }
}
