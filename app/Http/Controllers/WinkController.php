<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wink;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class WinkController extends Controller
{
    // Winks
    public function getWinks($page)
    {
        $user = auth()->user();
        $count = $user->winks()->count();
        
        $winks = $user->winks()->skip($page * 24)->take(24)->get();
        $users = array();

        foreach($winks as $w){

            $user = User::findOrFail($w->wink);

            array_push($users , $user);
        }

        $users = array_splice($users,0,24);

        $users = UserController::listUsersForView($users);

        return RespondHandler::respond(['status'=>true ,'count' => $count, 'users' => $users  , 'premium'=> true], 200);
    }

    public function getReverseWinks($page)
    {
        $user = auth()->user();
        $winks = Wink::query()->where('wink', $user->id)->get();
        $count = $winks->count();

        $users = array();

        foreach($winks as $w){

            $usera = User::findOrFail($w->user_id);

            array_push($users , $usera);
        }

        $users = array_splice($users,0,24);

        $users = UserController::listUsersForView($users);

        return RespondHandler::respond(['status'=>true ,'count' => $count, 'users' => $users,'premium'=> $user->is_premium ], 200);

    }

    public function getMutualWinks($page)
    {
        $user = auth()->user();
        $winks = $user->winks()->get();
        $winks2 = array();
        
        foreach ($winks as $wink) {
            $user2 = User::query()->where('id', $wink->wink)->first();
            if ($user2->winkedAt($user->id)) {
                array_push($winks2 , $wink);
            }
        }

        $count = count($winks2);

        $users = array();

        foreach($winks2 as $w){

            $usera = User::findOrFail($w->wink);

            array_push($users , $usera);
        }

        $users = array_splice($users,0,24);

        $users = UserController::listUsersForView($users);

        return RespondHandler::respond(['status'=>true ,'count' => $count, 'users' => $users , 'premium'=> $user->is_premium], 200);

    }

    public function wink($id)
    {
        $user = auth()->user();
        $user2 = User::find($id);

        $prev = $user->winks()->where('wink',$user2->id);
        $prev->delete();
        $wink = new Wink();
        $wink->wink = $id;
        $query = $user->winks()->save($wink);
        if ($query) {

            NotificationController::create($user2->id , $user->id,'wink', 'Winked At You');

            return RespondHandler::respond(['status'=>true],200);
        } else {
            return RespondHandler::respond(['status'=>false],200);
        }
    }


    public function dewink($id)
    {
        $user = auth()->user();
        $user2 = User::find($id);

        $wink = $user->winks()->where('wink',$user2->id);

        if ($wink) {

            $wink->delete();

            return RespondHandler::respond(['status'=>true],200);
        } else {
            return RespondHandler::respond(['status'=>false],200);
        }
    }


    public static function getRecentWinks()
    {

        $user = auth()->user();
        $winks = Wink::query()->where('wink', $user->id)->orderByDesc('created_at')->take(4)->get();
        $count = $winks->count();


        $new = array();
    
        foreach($winks as $wink){
            $u = User::find($wink->user_id);
            array_push($new, $u);
        }

        $new = UserController::listUsersForView($new);

        if ($user->is_premium) {
            return ['count' => $count, 'winks' => $new];
        } else {
            return ['count' => $count, 'upgrade' => true];
        }
    }


    public static function getWinksCount()
    {
        $user = auth()->user();
        $winks = Wink::query()->where('wink', $user->id)->get();

        return $winks->count();

    }

}
