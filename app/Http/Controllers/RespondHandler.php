<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RespondHandler extends Controller
{
    public static function respond($array, $code){

        return response()->json( $array, $code);

    }
    //200
    public static function ok(){return response()->json(['respond' => true], 200);}
    public static function notOk(){return response()->json(['respond' => false], 200);}
    public static function created(){return response()->json(['respond' => 'created'], 201);}
    public static function accepted(){return response()->json(['respond' => 'accepted'], 202);}
    public static function noContent(){return response()->json(['respond' => 'no content'], 204);}

    //400
    public static function badRequest(){return response()->json(['respond' => 'bad request'], 400);}
    public static function unauthorized(){return response()->json(['respond' => 'unauthorized'], 401);}
    public static function noAccess(){return response()->json(['respond' => 'no access'], 403);}
    public static function premium(){return response()->json(['respond' => 'upgrade'], 403);}
    public static function notFound(){return response()->json(['respond' => 'not found'], 404);}



}
