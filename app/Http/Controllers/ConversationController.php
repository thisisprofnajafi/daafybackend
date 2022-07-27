<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\User;
use http\Message;
use Illuminate\Http\Request;
use App\Events\NewChatMessage;

class ConversationController extends Controller
{


    public function createNewConversation($id , Request $request){

        $user = auth()->user();
        $user2 = User::findOrFail($id);

        if($user->is_premium){
                $conversation = new Conversation();
                $conversation->user_a = $user->id;
                $conversation->user_b = $user2->id;
                
                $conversation->save();
                $other_id = ($conversation->user_a == auth()->user()->id) ? $conversation->user_b : $conversation->user_a;


                $conv = $conversation;
                $newMessage = new ChatMessage();
                $newMessage->user_id = auth()->user()->id;
             
                $newMessage->conversation_id = $conv->id;
                $newMessage->message = $request->message;
                $newMessage->to = $other_id;
                $query = $newMessage->save();

            return RespondHandler::respond(['status' => true], 200);
        }else{
            return RespondHandler::respond(['status' => false, 'error' => 'premium'], 200);
        }
    }


    public function conversations()
    {
        $user = auth()->user();
        $conversations =  Conversation::query()->where('user_a', $user->id)->orWhere('user_b', $user->id)->orderByDesc('created_at')->get();
        $conversationsNew = array();
        foreach($conversations as $conv){
            $userToGetAvatar = ($user->id == $conv->user_a) ? $conv->user_b : $conv->user_a;
            $avatar = User::query()->where('id' , $userToGetAvatar)->first();
            $avatar = $avatar->avatar;
            if($conv->messages()->count() > 0){
                $lastMessage = $conv->messages()->orderByDesc('created_at')->first();
            }
            
            $not_seen = $conv->messages()->where('seen',false)->where('to',$user->id);
            $has_unseen = ($not_seen->count() > 0) ? true : false  ;
            array_push($conversationsNew , ['id'=>$conv->id,'avatar'=>$avatar,'lastMessage'=> ($lastMessage) ? $lastMessage->created_at->diffForHumans() : '','not_seen'=>$has_unseen]);
        }
        return RespondHandler::respond(['status' => true , 'conversations'=>$conversationsNew], 200);
    }

    public function newMessage(Request $request, $id)
    {

        $user = auth()->user();
        $conv = Conversation::find($id);
        $newMessage = new ChatMessage();
        $newMessage->user_id = auth()->user()->id;
        $newMessage->conversation_id = $conv->id;
        $newMessage->message = $request->message;

        $other_id = ($conv->user_a == auth()->user()->id) ? $conv->user_b : $conv->user_a;

        $newMessage->to = $other_id;

        $query = $newMessage->save();

        broadcast(new NewChatMessage($newMessage));

        $user2 = User::find($other_id);

        if($user2->last_seen < Carbon::now()->addMinutes(10))

            NotificationController::create($user2->id , $user->id,'message', 'Sent You A Message');

        RespondHandler::respond(['status' => true , 'message'=>$newMessage], 200);
    }

    public function markAsReed($id){
        $user = auth()->user();

        $conv = Conversation::find($id);

        $messages = $conv->messages()->where('to' , $user->id)->where('seen' , false)->get();
        if($messages){
            foreach($messages as $message){

                $message->seen = true;

                $message->save();

            }
        }
        return RespondHandler::respond(['status'=>true],200);

    }


    public function messages($id){

        $conv = Conversation::find($id);
        $messages = ChatMessage::query()->where('conversation_id',$id)->get();
        $new = array();
        if ($conv->user_a == auth()->user()->id || $conv->user_b == auth()->user()->id ){

            foreach($messages as $message){

                $user = User::find($message->user_id);

                array_push($new , [
                    'id'=>$id,
                    'avatar'=>$user->avatar,
                    'to'=>$message->to,
                    'content'=> [
                        'text'=>$message->message,
                        'time'=>$message->created_at->diffForHumans()
                    ],
                    'sender'=>($message->user_id == auth()->user()->id) ? 'self' : 'other',
                    'type'=>'message'
                ]);

            }

            return RespondHandler::respond(['status'=>true,'messages'=>$new ,'auth'=>auth()->user()->id],200);

        }
        return RespondHandler::respond(['status'=>false],200);

    }



    public function checkConverstiaion($id){

        $user = auth()->user();

        $user2 = User::findOrFail($id);

        if($user->is_premium){
        
        $prev = Conversation::query()->whereIn('user_a', [$user->id, $user2->id])->whereIn('user_b', [$user->id, $user2->id])->first();
            if($prev){
                return RespondHandler::respond(['status'=>true , 'hasConv' => true , 'convId'=>$prev->id],200);
            }else{
                return RespondHandler::respond(['status'=>true , 'hasConv' => false],200);
            }
            
        }
        return RespondHandler::respond(['status'=>false],200);
    }




    public static function getUnreadMessagesCount(){
        $user = auth()->user();

        $messages = ChatMessage::query()->where('to',$user->id)->where('seen',false)->get();

        return $messages->count();
    }
}
