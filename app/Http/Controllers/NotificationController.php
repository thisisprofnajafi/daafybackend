<?php

namespace App\Http\Controllers;

use App\Events\NotificationEvent;
use App\Mail\NotificationEmail;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{

    public static function create($user_id , $user_action_id, $type, $message)
    {

        $user = User::find($user_id);
        $user_action = User::find($user_action_id);
        $notification = new Notification();

        $notification->action_username = $user_action->username;
        $notification->type = $type;
        $notification->text = $message;

        $user->notifications()->save($notification);

        if ($user->last_seen <= Carbon::now()){
            Mail::to($user->email)->send(new NotificationEmail($notification , $user));
        }

    }

    public static function getNotificationsMin()
    {

        $user = auth()->user();

        $notes = array();
        $text = null;
        $link = null;
        $avt = null;
        $notifications = $user->notifications()->orderBy('created_at')->get();

        $count = $notifications->where('seen', false)->count();
        $notification = $notifications->take(5);

        foreach ($notifications as $note) {

            $userb = User::query()->where('username' , $note->action_username)->first();
            $name = $userb->firstname;
            $username = $userb->username;
            $avatar = $userb->avatar;


            if (!$user->is_premium) {
                if ($note->type == 'wink') {

                    $text = 'A User Just Winked At You';
                    $u = null;
                }
                if ($note->type == 'fave') {

                    $text = 'A User Just Added You To Favorites';
                    $u = null;
                }
                if ($note->type == 'view') {

                    $text = 'A User Just Viewed Your Profile';
                    $u = null;
                }
                if ($note->type == 'message') {

                    $text = ucfirst($name) . ' (' . $username . ') ' . 'Just Sent You A Message';
                    $avt = $avatar;
                    $u = $userb;
                }

                if ($note->type == 'request') {

                    $text = ucfirst($name) . ' (' . $username . ') ' . 'Requested To Be Fiend With You';
                    $avt = $avatar;
                    $u = $userb;

                }

                if ($note->type == 'request-accept') {

                    $text = ucfirst($name) . ' (' . $username . ') ' . 'Accepted You Friendship Request';
                    $avt = $avatar;
                    $u = $userb;
                }

                array_push($notes, [
                    'id'=>$note->id,
                    'message' => $text,
                    'avatar' => $avt,
                    'seen'=>$note->seen,
                    'type'=>$note->type,
                    'user'=>$u,
                    'time'=>$note->created_at->diffForHumans()
                ]);

            } else {

                if ($note->type == 'wink') {

                    $text = ucfirst($name) . ' (' . $username . ') ' . 'Just Winked At You';
                    $avt = $avatar;
                    $u = $userb;
                }
                if ($note->type == 'fave') {

                    $text = ucfirst($name) . ' (' . $username . ') ' . 'Just Added You To Favorites';
                    $avt = $avatar;
                    $u = $userb;
                }
                if ($note->type == 'view') {

                    $text = ucfirst($name) . ' (' . $username . ') ' . 'Just Viewed Your Profile';
                    $avt = $avatar;
                    $u = $userb;
                }
                if ($note->type == 'message') {

                    $text = ucfirst($name) . ' (' . $username . ') ' . 'Just Sent You A Message';
                    $avt = $avatar;
                    $u = $userb;
                }
                if ($note->type == 'request') {
                    $text = ucfirst($name) . ' (' . $username . ') ' . 'Sent a Friend Request';
                    $avt = $avatar;
                    $u = $userb;
                }

                if ($note->type == 'request-accept') {

                    $text = ucfirst($name) . ' (' . $username . ') ' . 'Accepted You Friendship Request';
                    $avt = $avatar;
                    $u = $userb;
                }


                array_push($notes, [
                    'id'=>$note->id,
                    'message' => $text,
                    'avatar' => $avt,
                    'seen'=>$note->seen,
                    'type'=>$note->type,
                    'user'=>$u,
                    'time'=>$note->created_at->diffForHumans()
                ]);
            }
        }
        return RespondHandler::respond(['status' => true, 'count' => $count, 'is_premium' => $user->is_premium, 'notifications' => $notes ], 200);
    }

    public function getNotifications($load)
    {
        $user = auth()->user();

        $notes = array();
        $text = null;
        $link = null;
        $avt = null;
        $notifications = $user->notifications()->orderByDesc('created_at')->take(($load+1)*30)->get();
        $countAll = $user->notifications()->count();
        $load = $notifications->count();


        foreach ($notifications as $note) {

            $userb = User::query()->where('username' , $note->action_username)->first();
            $name = $userb->firstname;
            $username = $userb->username;
            $avatar = $userb->avatar;

            if (!$user->is_premium) {
                if ($note->type == 'wink') {

                    $text = 'A User Just Winked At You';
                    $link = 'winks-reverse';
                    $u = null;
                }
                if ($note->type == 'fave') {

                    $text = 'A User Just Added You To Favorites';
                    $link = 'favorites-reverse';
                    $u = null;
                }
                if ($note->type == 'view') {

                    $text = 'A User Just Viewed Your Profile';
                    $link = 'view-viewer';
                    $u = null;
                }
                if ($note->type == 'message') {

                    $text = ucfirst($name) . ' (' . $username . ') ' . 'Just Sent You A Message';
                    $avt = $avatar;
                    $link = 'message';
                    $u = $userb;
                }

                if ($note->type == 'request') {

                    $text = ucfirst($name) . ' (' . $username . ') ' . 'Requested To Be Fiend With You';
                    $link = 'requests';
                    $avt = $avatar;
                    $u = $userb;

                }

                if ($note->type == 'request-accept') {

                    $text = ucfirst($name) . ' (' . $username . ') ' . 'Accepted You Friendship Request';
                    $link = 'user-profile-' . $userb->username;
                    $avt = $avatar;
                    $u = $userb;
                }

                array_push($notes, [
                    'id'=>$note->id,
                    'message' => $text,
                    'avatar' => $avt,
                    'seen'=>$note->seen,
                    'type'=>$note->type,
                    'user'=>$u,
                    'date'=>$note->created_at->isoFormat('MMM Do YY'),
                    'time'=>$note->created_at->isoFormat('h:mm a')
                ]);




            } else {

                if ($note->type == 'wink') {

                    $text = ucfirst($name) . ' (' . $username . ') ' . 'Just Winked At You';
                    $link = 'winks-reverse';
                    $avt = $avatar;
                    $u = $userb;
                }
                if ($note->type == 'fave') {

                    $text = ucfirst($name) . ' (' . $username . ') ' . 'Just Added You To Favorites';
                    $link = 'favorites-reverse';
                    $avt = $avatar;
                    $u = $userb;
                }
                if ($note->type == 'view') {

                    $text = ucfirst($name) . ' (' . $username . ') ' . 'Just Viewed Your Profile';
                    $link = 'view-viewer';
                    $avt = $avatar;
                    $u = $userb;
                }
                if ($note->type == 'message') {

                    $text = ucfirst($name) . ' (' . $username . ') ' . 'Just Sent You A Message';
                    $link = 'message';
                    $avt = $avatar;
                    $u = $userb;
                }
                if ($note->type == 'request') {
                    $text = ucfirst($name) . ' (' . $username . ') ' . 'Requested To Be Fiend With You';
                    $link = 'requests';
                    $avt = $avatar;
                    $u = $userb;
                }
                if ($note->type == 'request-accept') {

                    $text = ucfirst($name) . ' (' . $username . ') ' . 'Accepted You Friendship Request';
                    $link = 'user-profile-' . $userb->username;
                    $avt = $avatar;
                    $u = $userb;
                }

                array_push($notes, [
                    'id'=>$note->id,
                    'message' => $text,
                    'avatar' => $avt,
                    'seen'=>$note->seen,
                    'type'=>$note->type,
                    'user'=>$u,
                    'date'=>$note->created_at->isoFormat('MMM Do YY'),
                    'time'=>$note->created_at->isoFormat('h:mm a')
                ]);


            }
        }
        return RespondHandler::respond(['status' => true, 'countAll' => $countAll,'load'=>$load, 'is_premium' => $user->is_premium, 'notifications' => $notes], 200);
    }


    public function markAsSeen($load)
    {
        $notifications = auth()->user()->notifications()->where('seen', false)->take(($load+1)*30)->get();

        foreach ($notifications as $note) {
            $note->seen = true;
            $note->save();
        }
        return RespondHandler::respond(['status' => true],200);
    }


    public function markAsSeenMini()
    {
        $notifications = auth()->user()->notifications()->where('seen', false)->orderByDesc('created_at')->take(4)->get();

        foreach ($notifications as $note) {
            $note->seen = true;
            $note->save();
        }

        return RespondHandler::respond(['status' => true],200);

    }
}
