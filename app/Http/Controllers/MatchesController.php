<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class MatchesController extends Controller
{
    //Matches
    public function getMatches($page)
    {
        $user = auth()->user();

        $users = User::query()->where('gender', $user->interests->gender)->where('id','!=',$user->id)->get();
        $matches = array();

        foreach($users as $key => $u){
            if($user->isFriendWith($u)){
                $users->forget($key);
            }
        }

        foreach($users as $u){
            if($user->isMatching($u)['matching'] && !$user->hasBlocked($u) && !$user->isBlockedBy($u))
                array_push($matches , $u);
        }

        $newList = UserController::listUsersForView($matches);

        $count = count($newList);

        $newList = array_slice($newList, $page * 24, 24);


        return RespondHandler::respond(['status' =>true , 'count' => $count, 'users' => $newList , 'premium'=> true], 200);
        
    }

    public function getReverseMatches( $page)
    {
        $user = auth()->user();

        $users = User::query()->where('id','!=',$user->id)->get();

        foreach ($users as $key => $u) {
            if ($u->interests->gender !== $user->gender){
                $users->forget($key);
            }
        }

        foreach ($users as $key => $u) {
            if ($u->isMatching($user)['n'] < 5){
                $users->forget($key);
            }
        }

        foreach($users as $key => $u){
            if($ur->isFriendWith($user)){
                $users->forget($key);
            }
        }


        $count = $users->count();
        $matches = $users->skip($page*24)->take(24);
        if ($count > 0) {
            return RespondHandler::respond(['count' => $count, 'reverse' => $matches ,'premium'=> $user->is_premium], 200);
        } else {
            return RespondHandler::noContent();
        }
    }

    public function getMutualMatches( $page)
    {

        $user = auth()->user();

        $matches = User::query()->where('gender',$user->interests->getnder)->where('id','!=',$user->id)->get();

        foreach ($matches as $key=>$m){
            if ($user->isMatching($m)['n'] < 5){
                $matches->forget($key);
            }
        }

        foreach ($matches as $key=>$m){
            if ($m->isMatching($user)['n'] < 5){
                $matches->forget($key);
            }
        }

        foreach($matches as $key => $u){
            if($user->isFriendWith($u)){
                $matches->forget($key);
            }
        }

        $count = $matches->count();
        $matches = $matches->skip($page*24)->take(24);
        if ($count > 0) {
            return RespondHandler::respond(['count' => $count, 'mutual' => $matches , 'premium'=> $user->is_premium], 200);
        } else {
            return RespondHandler::noContent();
        }

    }

    public static function getRecentMatches(){
        $user = auth()->user();

        $users = User::all()->where('id','!=',$user->id)->where('gender', $user->interests->gender);

        $matches = array();


        foreach($users as $key => $u){
            if($user->isFriendWith($u)){
                $users->forget($key);
            }
        }

        foreach ($users as $u) {

            $m = $user->isMatching($u);

            if ($m['matching'] && !$user->hasBlocked($u) && !$user->isBlockedBy($u)) {

                array_push($matches, ['user' => $u, 'n' => $m['n']]);

            }

        }

        $matches = $user->userSortMatches($matches);

        $newList = array();

        foreach ($matches as $match){

            array_push($newList , $match['user']);

        }

        $matches = UserController::listUsersForView($newList);

        $count = count($matches);

        $matches = array_slice($matches, 0,4);

        return ['count'=>$count,'matches' => $matches];

    }

}
