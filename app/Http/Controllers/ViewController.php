<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\View;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Integer;

class ViewController extends Controller
{
    // View
    public function getViews($page)
    {
        $user = auth()->user();

        $count = $user->views()->count();
        $views = $user->views;


        $users = array();

        foreach($views as $v){

            $user = User::findOrFail($v->view);

            array_push($users , $user);
        }

        $users = UserController::listUsersForView($users);

        $users = array_slice($users, $page * 24, 24);

        return RespondHandler::respond(['status'=>true ,'count' => $count, 'users' => $users ,'premium'=> true], 200);


    }

    public function getViewers($page)
    {
        $user = auth()->user();

            $views = View::query()->where('view', $user->id)->orderByDesc('created_at')->get();
            $count = $views->count();
            $views = $views->skip(24 * $page)->take(24);

            $users = array();

            foreach($views as $v){

                $user = User::findOrFail($v->user_id);

                array_push($users , $user);
            }
            $users = UserController::listUsersForView($users);
            return RespondHandler::respond(['status'=>true ,'count' => $count, 'users' => $users ,'premium'=> $user->is_premium], 200);


    }

    public function view($id)
    {

        $user = auth()->user();
        $user2 = User::findOrFail($id);

        if ($user && $user2) {

            $prevs = $user->views()->where('view',$user2->id);

            $prevs->delete();
            
            $view = new View();
            $view->view = $user2->id;
            $user->views()->save($view);

            
            $prevNot = $user2->notifications()->where('type' , 'view')->where('action_username' , $user->username)->where('seen',false);

            if(!$prevNot->count() > 0)
                NotificationController::create($user2->id , $user->id,'view', 'Viewed Your Profile');
            return RespondHandler::ok();
        } else {
            return RespondHandler::notFound();
        }
    }

    public static function getViewsCount()
    {
        $user = auth()->user();

        $views = View::query()->where('view', $user->id)->orderByDesc('created_at')->get();

        return  $views->count();

    }


    public static function getRecentViews()
    {
        $user = auth()->user();
        $views = View::query()->where('view', $user->id)->orderByDesc('created_at')->take(4)->get();
        $count = $views->count();

        $new = array();

        foreach($views as $view){
            array_push($new , $view->user);
        }

        $new = UserController::listUsersForView($new);


        if ($user->is_premium) {
            return ['count' => $count, 'views' => $new];
        } else {
            return ['count' => $count, 'upgrade' => true];
        }

    }



}
