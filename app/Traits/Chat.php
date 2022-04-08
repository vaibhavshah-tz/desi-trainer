<?php

namespace App\Traits;

use App\Models\ChatRoom;
use App\Models\ChatMessage;
use Carbon\Carbon;

trait Chat
{
    /**
     * Save the chat message data
     * First check the avalilable room and save the data
     * 
     * @param illuminate/Request $request
     * @return bool
     */
    public function saveMessages($request)
    {
        $conversationData = $chatMessage = $returnData = [];
        $conversationData = [
            'ticket_id' => $request['ticket_id'],
            'user_id' => $request['user_id'] ?? null,
            'customer_id' => $request['customer_id'] ?? null,
            'trainer_id' => $request['trainer_id'] ?? null,
            'room_id' => $request['room_id']
        ];
        $conversationId = $this->saveConversationId($conversationData);

        $chatMessage = [
            'sender_type' => $request['sender_type'],
            'message' => $request['message'],
            'file' =>  $request['file'] ?? null,
        ];

        $chatMessageSave = $conversationId->chatMessages()->create($chatMessage);
        $setNameAvatar = $this->setNameAvatar($chatMessageSave->sender_type, $conversationId);
        $returnData = [
            'success' => ($chatMessageSave) ? true : false,
            'message' => $chatMessageSave->message ?? '',
            'time' => ChatMessage::getTimezoneDate($chatMessageSave->created_at, $conversationId->ticket->timezone->timezone),
            'sender_name' => $setNameAvatar['full_name'] ?? '',
            'avavtar' => $setNameAvatar['avatat'] ?? '',
            'sender_type' => $chatMessageSave->sender_type ?? '',
            'receiver_type' => !empty($conversationId->customer_id) ? config('constants.SENDER_TYPE.CUSTOMER') : config('constants.SENDER_TYPE.TRAINER'),
            'file_url' => $chatMessageSave->file_url ?? '',
            'file_type' =>  $chatMessageSave->file_type ?? '',
            'chat_date' => Carbon::now()->format('Y-m-d') ?? '',
            'media_type' => $chatMessageSave->media_type ?? '',
            'id' => $chatMessageSave->id ?? ''
        ];

        return array_merge($conversationData, $returnData);
    }

    /**
     * Save the room id
     * room id is already there so it's return that id
     * 
     * @param $roomData array
     * @return room object
     */
    public function saveConversationId($roomData = [])
    {
        $roomData = array_filter($roomData);
        $checkRoomId = ChatRoom::where($roomData)->first();

        if (empty($checkRoomId)) {
            $checkRoomId = ChatRoom::create($roomData);
        }
        $checkRoomId = ChatRoom::where($roomData)->with([
            'user:id,first_name,last_name,avatar',
            'customer:id,first_name,last_name,avatar',
            'trainer:id,first_name,last_name,avatar',
            'ticket:id,timezone_id',
            'ticket.timezone:id,timezone'
        ])->first();

        return $checkRoomId;
    }

    /**
     * Set the name and avatar
     * 
     * @param $senderType integer
     * @param $userData oject
     * @return array
     */
    public function setNameAvatar($senderType, $userData)
    {
        $data = [];
        switch ($senderType) {
            case config('constants.SENDER_TYPE.USER'):
                $data['full_name'] = $userData->user->full_name;
                $data['avatat'] = url($userData->user->avatar_url);
                break;
            case config('constants.SENDER_TYPE.CUSTOMER'):
                $data['full_name'] = $userData->customer->full_name;
                $data['avatat'] = $userData->customer->avatar_url;
                break;
            case config('constants.SENDER_TYPE.TRAINER'):
                $data['full_name'] = $userData->trainer->full_name;
                $data['avatat'] = $userData->trainer->avatar_url;
                break;
        }

        return $data;
    }
}
