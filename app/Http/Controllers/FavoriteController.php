<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    // Faves
    public function getFaves($page)
    {

        $user = auth()->user();
            $count = $user->faves()->count();
            $faves = $user->faves()->orderByDesc('created_at')->skip($page * 24)->take(24)->get();
            $users = array();

        foreach($faves as $f){

            $user = User::findOrFail($f->fave);

            array_push($users , $user);
        }

        $users = array_splice($users,0,24);

        $users = UserController::listUsersForView($users);
        return RespondHandler::respond(['status'=>true ,'count' => $count, 'users' => $users , 'premium'=> true], 200);
        
    }

    public function getReverseFaves($page)
    {

        $user = auth()->user();

            $faves = Favorite::query()->where('fave', $user->id)->orderByDesc('created_at')->get();
            $count = $faves->count();

            $users = array();

            foreach($faves as $f){

                $usera = User::findOrFail($f->user_id);
    
                array_push($users , $usera);
            }
    
            $users = array_splice($users,0,24);
    
            $users = UserController::listUsersForView($users);

            return RespondHandler::respond(['status'=>true ,'count' => $count, 'users' => $users , 'premium'=> $user->is_premium], 200);
        

    }

    public function getMutualFaves($page)
    {

        $user = auth()->user();
        $faves = $user->faves()->get();
        $faves2 = array();

            foreach ($faves as $fave) {
                $user2 = User::query()->where('id', $fave->fave)->first();
                if ($user2->hasFaved($user->id)) {
                    array_push($faves2 , $fave);
                }
            }

            $count = count($faves2);

            $users = array();

            foreach($faves2 as $f){

                $usera = User::findOrFail($f->fave);
    
                array_push($users , $usera);
            }
    
            $count = count($users);

            $users = array_splice($users,0,24);

            $users = UserController::listUsersForView($users);
    
            return RespondHandler::respond(['status'=>true ,'count' => $count, 'users' => $users , 'premium'=> $user->is_premium], 200);
        
    }

    public function favorite($id)
    {
        $user = auth()->user();
        $user2 = User::find($id);


        $prev = $user->faves()->where('fave',$user2->id);
        $prev->delete();

        if (!$user->hasFavedUser($id)) {

            $fave = new Favorite();

            $fave->fave = $id;

            $query = $user->faves()->save($fave);

            if ($query) {

                NotificationController::create($user2->id , $user->id,'fave', 'Showed Fave To Your Profile');

                return RespondHandler::respond(['status'=>true],200);

            } else {
                return RespondHandler::respond(['status'=>false],200);
            }

        } else {
            return RespondHandler::respond(['status'=>true],200);
        }
    }

    public function unFavorite($id)
    {
        $user = auth()->user();

        if ($user->hasFavedUser($id)) {

            $fave = $user->faves()->where('fave', $id);
            $query = null;
            if ($fave) {
                $query = $fave->delete();
                if ($query) {
                    return RespondHandler::ok();
                } else {
                    return RespondHandler::noContent();
                }
            } else {
                return RespondHandler::ok();
            }

        } else {
            return RespondHandler::ok();
        }
    }


    public static function getFavesCount()
    {
        $user = auth()->user();

        $faves = Favorite::query()->where('fave', $user->id)->get();
        $count = $faves->count();
        return $count;

    }

    public static function getRecentFaves()
    {

        $user = auth()->user();


        $faves = Favorite::query()->where('fave', $user->id)->orderByDesc('created_at')->take(4)->get();

        $count = $faves->count();

        $new = array();


        foreach($faves as $fave){
            array_push($new , $fave->user);
        }



        $new = UserController::listUsersForView($new);

        if ($user->is_premium) {

            return ['count' => $count, 'faves' => $new];

        } else {

            return ['count' => $count, 'upgrade' => true];

        }


    }


}
