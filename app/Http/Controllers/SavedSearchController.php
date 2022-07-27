<?php

namespace App\Http\Controllers;

use App\Models\SavedSearch;
use Illuminate\Http\Request;

class SavedSearchController extends Controller
{

    public function save(Request $request){

        $request->validate([
            'name'=>'string|required',
            'link'=>'string|required'
        ]);

        $user = auth()->user();

        if ($user->savedSearches()->count() <=10 ){

            $search = new SavedSearch();
            $search->name = $request->name;
            $search->link = $request->link;

            $user->savedSearches()->save($search);

            return RespondHandler::respond(['status'=>true],200);

        }else{
            return RespondHandler::respond(['status'=>false],200);
        }
    }

    public function getSaves(){

        $user = auth()->user();
        $count = $user->savedSearches()->count();
        $saves = $user->savedSearches();
        return RespondHandler::respond(['count'=>$count,'saves'=>$saves],200);

    }

    public function deleteSave(Request $request){

        $request->validate([
            'id'=>'numeric|required'
        ]);

        $user = auth()->user();

        $saved = $user->savedSearches()->where('id',$request->id);

        $saved->delete();

        return RespondHandler::respond(['status'=>true],200);

    }















}
