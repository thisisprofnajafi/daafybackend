<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\DocBlock\Tag;

class SearchController extends Controller
{
    public function basic($page, Request $request)
    {


        $users = User::all()->where('gender', auth()->user()->interests->gender);

        if ($request->country)
            $users = $users->where('country', $request->country);
   

        if ($request->minAge && $request->minAge != 18) {
            foreach ($users as $key => $user) {
                if ($user->getAge() < $request->minAge)
                    $users->forget($key);
            }
        }

        if ($request->maxAge && $request->maxAge != 99) {
            foreach ($users as $key => $user) {
                if ($user->getAge() > $request->maxAge)
                    $users->forget($key);
            }
        }

        $count = $users->count();
        $users = $users->skip($page * 24)->take(24);

        $arrayList = array();

        foreach($users as $u){
            array_push($arrayList , $u);
        }

        $arrayList = UserController::listUsersForView($arrayList);

        return RespondHandler::respond(['status' => true, 'count' => $count, 'users' => $arrayList], 200);

    }

    public function advanced(Request $request, $page)
    {

        $users = User::all()->where('gender', auth()->user()->interests->gender);


        if ($request->country)
            $users = $users->where('country', $request->country);
        if ($request->state)
            $users = $users->where('state', $request->state);
        if ($request->city)
            $users = $users->where('city', $request->city);

        if ($request->lifestyle) {
            foreach ($users as $key => $user) {
                if (!in_array(strtolower($user->living_status) , $request->lifestyle))
                    $users->forget($key);
            }
        }

        if ($request->educationValue) {
            foreach ($users as $key => $user) {
                if (!in_array(strtolower($user->educations_status) , $request->educationValue))
                    $users->forget($key);
            }
        }

        if ($request->employment) {
            foreach ($users as $key => $user) {
                if (!in_array(strtolower($user->educations_status) , $request->employment))
                    $users->forget($key);
            }
        }
        if ($request->seekingFor) {
            foreach ($users as $key => $user) {
                if (count(array_intersect($user->interest->seeking_for,$request->seekingFor)) > 0)
                    $users->forget($key);
            }
        }
        if ($request->relation) {
            foreach ($users as $key => $user) {
                if (!in_array(strtolower($user->relationship_status) , $request->relation))
                    $users->forget($key);
            }
        }

        if ($request->smoke) {
            foreach ($users as $key => $user) {
                if (!in_array(strtolower($user->smoke) , $request->smoke))
                    $users->forget($key);
            }
        }

        if ($request->drink) {
            foreach ($users as $key => $user) {
                if (!in_array(strtolower($user->drink) , $request->drink))
                    $users->forget($key);
            }
        }

        foreach ($users as $key => $user) {
            if (!in_array($user->getAge() , range($request->minAge , $request->maxAge)))
                $users->forget($key);
        }

        foreach ($users as $key => $user) {
            if (!in_array($user->height , range($request->minHeight , $request->maxHeight)))
                $users->forget($key);
        }
 

        $count = $users->count();
        $users = $users->skip($page * 24)->take(24);


        $arrayList = array();

        foreach($users as $u){
            array_push($arrayList , $u);
        }

        $arrayList = UserController::listUsersForView($arrayList);


        return RespondHandler::respond(['status' => true, 'count' => $count, 'users' => $arrayList], 200);

    }

    public function tag(Request $request, $page)
    {
        $tags[] = $request->tags;

        $users = User::whereHas('tags', function ($q) use ($tags) {
            $q->whereIn('name', $tags);
        })->get();

        $count = $users->count();
        $users = $users->skip($page * 24)->take(24);

        return RespondHandler::respond(['count' => $count, 'users' => $users], 200);

    }
}
