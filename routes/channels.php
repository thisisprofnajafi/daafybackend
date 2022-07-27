<?php

use App\Http\Controllers\RespondHandler;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('chat.{id}',function ($user,$id){
    if (auth()->user()){

        return RespondHandler::respond(['data'=>auth()->user()->id,'name'=>$user->name],200);

    }
});



Broadcast::channel('notify.{id}',function ($user,$id){
    if (auth()->user()){

        return RespondHandler::respond(['data'=>auth()->user()->id,'name'=>$user->name],200);

    }
});
