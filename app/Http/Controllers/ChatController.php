<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatRoom;
use App\Models\Ticket;
use App\Models\Trainer;
use App\Traits\Chat;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    use Chat;

    /**
     * Get the all chat data
     * 
     * @param $request
     * @return object
     */
    public function index(Request $request)
    {
        $page_title = 'Chat';
        $messageFor = '';
        $chatMessages = [];
        $ticketId = $request->id;
        $interestedTrainer = $this->getInterestedTrainer($ticketId);

        $ticketDetails = Ticket::select('id', 'ticket_id', 'trainer_id', 'customer_id', 'status', 'timezone_id')
            ->with([
                'customer:id,first_name,last_name,avatar',
                'timezone:id,timezone',
                // 'trainer:id,first_name,last_name,avatar'
            ])->findorfail($ticketId);
        $trainerId = ($request->trainer_id) ? $request->trainer_id : $ticketDetails->trainer_id;

        if (!empty($trainerId)) {
            $trainer = Trainer::active()->select('id', 'first_name', 'last_name', 'avatar')
                ->findorfail($trainerId);
            $ticketDetails['trainer'] = $trainer;
        }
        // echo '<pre>';
        // print_r($ticketDetails->trainer_id);
        // die;
        $messagesData = ChatRoom::select('id', 'ticket_id', 'user_id', 'customer_id', 'trainer_id', 'room_id')
            ->with([
                'chatMessages:id,chat_room_id,sender_type,message,file,created_at',
                'user:id,first_name,last_name,avatar',
                'customer:id,first_name,last_name,avatar',
                'trainer:id,first_name,last_name,avatar'
            ])->where([
                ['ticket_id', $ticketId],
                ['user_id', '<>', null],
                ['trainer_id', $trainerId]
            ])->orWhere([
                ['ticket_id', $ticketId],
                ['user_id', '<>', null],
                ['customer_id', $ticketDetails->customer_id]
            ])->orderBy('id', 'DESC')->get();

        $msgCustomer = $msgTrainer = [];
        foreach ($messagesData as $roomKey => $roomValue) {
            if ($roomValue['customer_id']) {
                $messageFor = 'customerData';
                foreach ($roomValue->chatMessages as $msgKey => $msgValue) {
                    unset($roomValue['user']['id']);
                    $msgValue['time'] = ChatMessage::getTimezoneDate($msgValue['created_at'], $ticketDetails['timezone']['timezone']);
                    $data = array_merge($msgValue->toArray(), $roomValue['user']->toArray());
                    array_push($msgCustomer, $data);
                }
                unset($roomValue['chatMessages']);
                unset($roomValue['user']);
                $chatMessages[$messageFor][] = $roomValue;
            } else {
                $messageFor = 'trainerData';
                foreach ($roomValue->chatMessages as $msgKey => $msgValue) {
                    unset($roomValue['user']['id']);
                    $msgValue['time'] = ChatMessage::getTimezoneDate($msgValue['created_at'], $ticketDetails['timezone']['timezone']);
                    $data = array_merge($msgValue->toArray(), $roomValue['user']->toArray());
                    array_push($msgTrainer, $data);
                }
                unset($roomValue['chatMessages']);
                unset($roomValue['user']);
                $chatMessages[$messageFor][] = $roomValue;
            }
        }
        if (!empty($chatMessages['customerData'])) {
            array_multisort($msgCustomer, SORT_ASC);
            $data = collect($msgCustomer);
            $chatMessages['customerData']['chatMessages'] = $data;
        }
        if (!empty($chatMessages['trainerData'])) {
            array_multisort($msgTrainer, SORT_ASC);
            $data = collect($msgTrainer);
            $chatMessages['trainerData']['chatMessages'] = $data;
        }

        return view('chats.index', compact(
            'chatMessages',
            'page_title',
            'ticketId',
            'ticketDetails',
            'trainerId',
            'interestedTrainer'
        ));
    }

    /**
     * Save the message
     * Send the event to socket server
     * 
     * @param $request
     * @return json
     */
    public function saveMessage(Request $request)
    {
        return $this->saveMessages($request);
    }

    public function chatHtml(Request $request)
    {
        return view('chats.chats');
    }

    /**
     * Get the interested trainer liast with chat count
     * 
     * @param $ticketId
     * @return object
     */
    public function getInterestedTrainer($ticketId)
    {
        $interestedTrainer = Ticket::select('id', 'trainer_id', 'customer_id')
            ->with([
                'trainer:id',
                'customer:id',
                'interestedTrainers' => function ($q) {
                    $q->select('trainers.id', 'trainers.first_name', 'trainers.last_name', 'trainers.avatar');
                }, 'interestedTrainers.chatRooms' => function ($q) use ($ticketId) {
                    $q->select('id', 'ticket_id', 'user_id', 'trainer_id', 'room_id')->where([
                        ['user_id', '=', Auth::user()->id],
                        ['trainer_id', '<>', null],
                        ['ticket_id', '=', $ticketId],
                    ])->withCount(['chatMessages' => function ($q) {
                        $q->where([
                            ['sender_type', '<>', 1],
                            ['is_read', '<>', config('constants.READ_NOTIFICATION')]
                        ]);
                    }]);
                }, 'trainer.chatRooms' => function ($q) use ($ticketId) {
                    $q->select('id', 'ticket_id', 'user_id', 'trainer_id', 'room_id')->where([
                        ['user_id', '=', Auth::user()->id],
                        ['trainer_id', '<>', null],
                        ['ticket_id', '=', $ticketId],
                    ])->withCount(['chatMessages' => function ($q) {
                        $q->where([
                            ['sender_type', '<>', 1],
                            ['is_read', '<>', config('constants.READ_NOTIFICATION')]
                        ]);
                    }]);
                }, 'customer.chatRooms' => function ($q) use ($ticketId) {
                    $q->select('id', 'ticket_id', 'user_id', 'customer_id', 'room_id')->where([
                        ['user_id', '=', Auth::user()->id],
                        ['customer_id', '<>', null],
                        ['ticket_id', '=', $ticketId],
                    ])->withCount(['chatMessages' => function ($q) {
                        $q->where([
                            ['sender_type', '<>', 1],
                            ['is_read', '<>', config('constants.READ_NOTIFICATION')]
                        ]);
                    }]);
                }
            ])
            ->find($ticketId);

        return $interestedTrainer;
    }

    /**
     * Update the unread chat message
     * 
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function chatRead(Request $request)
    {
        if ($request->ajax()) {
            $chatRoom = ChatRoom::where("room_id", $request->room_id)
                ->where("user_id", Auth::user()->id)
                ->withCount(["chatMessages" => function ($query) {
                    $query->where('sender_type', '<>', 1);
                }])->first();

            if ($chatRoom && $chatRoom->chat_messages_count > 0) {
                ChatMessage::where('chat_room_id', $chatRoom->id)
                    ->where('sender_type', '<>', config('constants.SENDER_TYPE.USER'))
                    ->update(['is_read' => config('constants.READ_NOTIFICATION')]);

                return $chatRoom->room_id;
            }
        }

        return false;
    }
}
