<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChatRequest;
use App\Models\Chat;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    /**
     * @return JsonResponse
     */

    //Return all chats
    public function index(): JsonResponse
    {
        $chats = Chat::hasParticipant(auth()->user()->id)
            ->whereHas('messages')
            ->with('lastMessage.user', 'participants.user')
            ->latest('updated_at')
            ->get();
        return $this->success($chats);
    }



    /**
     * @param StoreChatRequest $request
     * @return array
     */

    //Prepares data to be stored
    private function prepareStoreData(StoreChatRequest $request) : array
    {
        $data = $request->validated();
        $other_user_id = (int)$data['user_id'];
        unset($data['user_id']);
        $data['created_by'] = auth()->user()->id;

        return [
            'other_user_id' => $other_user_id,
            'user_id' => auth()->user()->id,
            'data' => $data,
        ];
    }



    /**
     * @param int $other_user_id
     * @return mixed
     */

    //Check if user and other user has previous chat or not
    private function getPreviousChat(int $other_user_id) : mixed {

        $user_id = auth()->user()->id;

        return Chat::whereHas('participants', function ($query) use ($user_id){
                $query->where('user_id',$user_id);
            })
            ->whereHas('participants', function ($query) use ($other_user_id){
                $query->where('user_id',$other_user_id);
            })
            ->first();
    }



    /**
     * @param StoreChatRequest $request
     * @return JsonResponse
     */

    //Create new chat
    public function store(StoreChatRequest $request) : JsonResponse
    {
        $data = $this->prepareStoreData($request);
        if($data['user_id'] === $data['other_user_id']){
            return $this->error('You can not create a chat with your own');
        }

        $previousChat = $this->getPreviousChat($data['other_user_id']);

        if($previousChat === null){

            $chat = Chat::create($data['data']);
            $chat->participants()->createMany([
                [
                    'user_id'=>$data['user_id']
                ],
                [
                    'user_id'=>$data['other_user_id']
                ]
            ]);

            $chat->refresh()->load('lastMessage.user','participants.user');
            return $this->success($chat);
        }

        return $this->success($previousChat->load('lastMessage.user','participants.user'));
    }


    /**
     * @param Chat $chat
     * @return JsonResponse
     */

    //Return single chat
    public function show(Chat $chat): JsonResponse
    {
        $chat->load('lastMessage.user', 'participants.user');
        return $this->success($chat);
    }


}
