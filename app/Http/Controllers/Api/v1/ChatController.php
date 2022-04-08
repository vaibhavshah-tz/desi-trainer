<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\Ticket;
use Auth;
use App\Traits\Chat;
use Carbon\Carbon;
use App\Http\Requests\ChatReadRequest;

class ChatController extends ApiController
{
    use Chat;

    /**
     * Get all messages with sender and receiver details
     * URL - {{local}}/v1/trainer/chat
     * Method - GET
     * 
     * @return all chat data
     */
    public function getMessages(Request $request)
    {
        try {
            $userCondition = $messages = $chatData = $dateKeyChat = $dateWithChat = $setDataWithMessage =  [];
            $messageFor = '';
            $counter = 0;
            $currentDate = Carbon::now();

            if (Auth::guard('trainer')->check()) {
                $loggedinId = Auth::guard('trainer')->user()->id;
                $userCondition = ['trainer_id', '=', $loggedinId];
            } else {
                $loggedinId = Auth::guard('customer')->user()->id;
                $userCondition = ['customer_id', '=', $loggedinId];
            }

            $chatMessages = Ticket::select('id', 'user_id', 'customer_id', 'trainer_id', 'ticket_id', 'timezone_id')
                ->with([
                    'chatRooms' => function ($q) use ($userCondition) {
                        $q->select('id', 'ticket_id', 'user_id', 'customer_id', 'trainer_id', 'room_id', 'status')
                            ->where([$userCondition]);
                    },
                    'chatRooms.chatMessages' => function ($q) {
                        $q->select('id', 'chat_room_id', 'sender_type', 'message', 'file', 'is_read', 'created_at')
                            ->orderBy('created_at', 'DESC');
                    },
                    'user:id,first_name,last_name,avatar',
                    'customer:id,first_name,last_name,avatar',
                    'trainer:id,first_name,last_name,avatar',
                    'timezone:id,timezone'
                ])->where([
                    ['id', $request->ticket_id]
                ])->get()->toArray();

            foreach ($chatMessages as $ticketKey => $ticketValue) {
                if (!empty($ticketValue['chat_rooms'])) {
                    foreach ($ticketValue['chat_rooms'] as $roomKey => $roomValue) {
                        if ($roomValue['user_id']) {
                            $messageFor  = 'admin';
                            unset($chatData);
                            $chatData = $dateKeyChat = $setDataWithMessage = [];
                            $counter = 0;
                            $messages[$messageFor] = $roomValue;
                            unset($messages[$messageFor]['chat_messages']);
                        } else {
                            unset($chatData);
                            $chatData = $dateKeyChat = $setDataWithMessage = [];
                            $counter = 0;
                            $messageFor = (Auth::guard('trainer')->check()) ? 'customer' : 'trainer';
                            $messages[$messageFor] = $roomValue;
                            unset($messages[$messageFor]['chat_messages']);
                        }
                        foreach ($roomValue['chat_messages'] as $messageKey => $messageValue) {
                            $messageValue['time'] = ChatMessage::getTimezoneDate($messageValue['created_at'], $ticketValue['timezone']['timezone']);
                            array_push($chatData, $messageValue);
                            $createdDate = strtok($chatData[$counter]['created_at'], "T");
                            $dateKeyChat[$createdDate][] = $messageValue;

                            $counter++;
                        }

                        foreach ($dateKeyChat as $dateKeyChatKey => $dateKeyChatValue) {
                            $dateWithChat['chatMessage'] = $dateKeyChat[$dateKeyChatKey];
                            $dateWithChat['chatDate'] = ($dateKeyChatKey === $currentDate->toDateString()) ? 'Today' : $dateKeyChatKey;
                            array_push($setDataWithMessage, $dateWithChat);
                        }

                        $messages[$messageFor]['messages'] = $setDataWithMessage;
                    }
                }
            }
            unset($ticketValue['chat_rooms']);
            $messages['ticketDetails'] = $ticketValue;

            return $this->apiResponse->respondWithMessageAndPayload($messages);
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Save the message
     * Send the data to socket server
     * URL - {{local}}/v1/trainer/chat
     * Method - POST
     * 
     * @param $request
     * @return json
     */
    public function saveMessage(Request $request)
    {
        if (Auth::guard('trainer')->check()) {
            $loggedinId = Auth::guard('trainer')->user()->id;
            $request->merge(['trainer_id' => $loggedinId]);
        } else {
            $loggedinId = Auth::guard('customer')->user()->id;
            $request->merge(['customer_id' => $loggedinId]);
        }

        $this->saveMessages($request);
    }

    /**
     * Set the chat read unread message
     * URL - {{local}}/v1/trainer/chat/mark-as-read
     * Method - POST
     * 
     * @param \App\Http\Requests\StoreCustomer $request
     * @return JsonResponse
     */
    public function markAsRead(ChatReadRequest $request)
    {
        try {
            $chatMesage = ChatMessage::where([
                ['chat_room_id', $request->chat_room_id],
                ['sender_type', $request->receiver_type]
            ])->update(['is_read' => $request->is_read]);

            if ($chatMesage) {
                return $this->apiResponse->respondWithMessageAndPayload($chatMesage, __("Chat mark as read successfully"));
            }

            return $this->apiResponse->respondWithError(__("Something went wrong!"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Get the unread message count
     * URL - {{local}}/v1/trainer/chat/unread-count?ticket_id=25
     * Method - GET
     * 
     * @param \App\Http\Requests\StoreCustomer $request
     * @return JsonResponse
     */
    public function getUnreadCount(Request $request)
    {
        try {
            $count = [];
            $count['count'] = 0;
            if (Auth::guard('trainer')->check()) {
                $loggedinId = Auth::guard('trainer')->user()->id;
                $userCondition = ['trainer_id', '=', $loggedinId];
                $senderType = [config('constants.SENDER_TYPE.USER'), config('constants.SENDER_TYPE.CUSTOMER')];
            } else {
                $loggedinId = Auth::guard('customer')->user()->id;
                $userCondition = ['customer_id', '=', $loggedinId];
                $senderType = [config('constants.SENDER_TYPE.USER'), config('constants.SENDER_TYPE.TRAINER')];
            }
            $chatMesageCount = ChatRoom::select(
                "id",
                "ticket_id",
                "trainer_id",
                "customer_id"
            )->withCount(['chatMessages as unread_chat_count' => function ($q) use ($senderType) {
                $q->whereIn('sender_type', $senderType)
                    ->where('is_read', config('constants.UNREAD_NOTIFICATION'));
            }])->where([
                ['ticket_id', $request->ticket_id],
                $userCondition
            ])->get();

            if ($chatMesageCount->isNotEmpty()) {
                foreach ($chatMesageCount as $store) {
                    $count['count'] += $store->unread_chat_count;
                }
            }

            return $this->apiResponse->respondWithMessageAndPayload($count, __("Chat unread message count"));

            // return $this->apiResponse->respondWithError(__("Something went wrong!"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }
}
