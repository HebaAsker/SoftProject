<?php

namespace App\Http\Controllers\Chat;

use App\Models\Chat;
use App\Models\User;
use App\Events\NewMessage;
use App\Models\ChatMessage;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetMessageRequest;
use App\Http\Requests\StoreMessageRequest;

class ChatMessageController extends Controller
{
    /**
     * @param GetMessageRequest $request
     * @return JsonResponse
     */

    //Return whole chat messages
    public function index(GetMessageRequest $request) :JsonResponse
    {
        $data = $request->validated();
        $chat_id = $data['chat_id'];
        $old_chat = $data['messages'];
        $chat_size = $data['chat_size'] ?? 10;

        $messages = ChatMessage::where('chat_id', $chat_id)
        ->with('user')
        ->latest('created_at')
        ->simplePaginate(
            $old_chat,
            ['*'],
            'page',
            $chat_size
        );
        return $this->success($messages->getCollection());
    }


    /**
     * @param StoreMessageRequest $request
     * @return JsonResponse
     */

    //Create chat messages
    public function store(StoreMessageRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id;

        $chat_message = ChatMessage::create($data);
        $chat_message->load('user');

        //Send broadcast event to pusher and send notification to one signal
        $this->sendNotificationToOther($chat_message);

        return $this->success($chat_message,'Message has been sent successfully.');
    }


     /**
     * Send notification to other users
     *
     * @param ChatMessage $chat_message
     */
    private function sendNotificationToOther(ChatMessage $chat_message) : void {

        //Move this event broadcast to observer
        broadcast(new NewMessage($chat_message))->toOthers();

        $user = auth()->user();
        $user_id = $user->id;

        $chat = Chat::where('id',$chat_message->chat_id)
            ->with(['participants'=>function($query) use ($user_id){
                $query->where('user_id','!=',$user_id);
            }])
            ->first();
        if(count($chat->participants) > 0){
            $other_user_id = $chat->participants[0]->user_id;

            $otherUser = User::where('id',$other_user_id)->first();
            $otherUser->sendNewMessageNotification([
                'messageData'=>[
                    'sender_name'=>$user->user_name,
                    'message'=>$chat_message->message,
                    'chat_id'=>$chat_message->chat_id
                ]
            ]);

        }

    }

}
