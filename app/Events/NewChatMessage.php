<?php

namespace App\Events;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chatMessage;

    public function __construct(ChatMessage $chatMessage)
    {

        $messageNew = $chatMessage;
        $user = User::find($messageNew->user_id);
        $message = [
            'id'=>$messageNew->id,
            'conversation_id'=>$messageNew->conversation_id,
            'to'=>$messageNew->to,
            'avatar'=>$user->avatar,
            'content'=> [
                'text'=>$messageNew->message,
                'time'=>$messageNew->created_at->diffForHumans()
            ],
            'sender'=>($messageNew->user_id == auth()->user()->id) ? 'self' : 'other',
            'type'=>'message'
        ];

        $this->chatMessage = $message;

    }
    public function broadcastOn()
    {
        return ['chat.'.$this->chatMessage['conversation_id']];
    }

    public function broadcastAs()
    {
        return 'daddychat';
    }
}
