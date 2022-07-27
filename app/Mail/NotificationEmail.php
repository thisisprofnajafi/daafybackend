<?php

namespace App\Mail;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $notification;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Notification $notification, User $user)
    {
        $this->notification = $notification;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $note = $this->notification;
        $user = $this->user;
        $user_action = User::all()->where('username',$note->action_username)->first();
        $name = $user_action->firstname;
        $username = $user_action->username;
        $avatar = $user_action->avatar;
        $text = null;
        $link = null;
        $avt = null;
        if (!$user->is_premium) {
            if ($note->type == 'wink') {

                $text = 'A User Just Winked At You';
                $avt = '';
            }
            if ($note->type == 'fave') {

                $text = 'A User Just Added You To Favorites';
                $avt = '';
            }
            if ($note->type == 'view') {

                $text = 'A User Just Viewed Your Profile';
                $avt = '';
            }
            if ($note->type == 'message') {

                $text = $name . '(' . $username . ') ' .'Sent You A Message';
                $avt = $avatar;
            }
            if ($note->type == 'request') {

                $text = $name . '(' . $username . ') ' . 'Requested To Be Fiend With You';
                $avt = $avatar;

            }
            if ($note->type == 'request-accept') {

                $text = $name . '(' . $username . ') ' . 'Accepted You Friendship Request';
                $avt = $avatar;
            }


        } else {
            if ($note->type == 'wink') {

                $text = $name . '(' . $username . ') ' . 'Just Winked At You';
                $link = 'winks-reverse';
                $avt = $avatar;
            }
            if ($note->type == 'fave') {

                $text = $name . '(' . $username . ') ' . 'Just Added You To Favorites';
                $avt = $avatar;
            }
            if ($note->type == 'view') {

                $text = $name . '(' . $username . ') ' . 'Just Viewed Your Profile';
                $avt = $avatar;
            }
            if ($note->type == 'message') {

                $text = $name . '(' . $username . ') ' . 'Just Sent You A Message';
                $avt = $avatar;
            }
            if ($note->type == 'request') {
                $text = $name . '(' . $username . ') ' . 'Requested To Be Fiend With You';
                $avt = $avatar;
            }
            if ($note->type == 'request-accept') {

                $text = $name . '(' . $username . ') ' . 'Accepted You Friendship Request';
                
                $avt = $avatar;
            }
        }


        return $this->view('notification')->with(['text' => $text, 'avatar' => $avt , 'name'=>$user->firstname]);
    }
}
