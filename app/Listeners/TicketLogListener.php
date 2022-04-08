<?php

namespace App\Listeners;

use App\Events\TicketLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Notification;
use App\Models\UserDevice;

class TicketLogListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SendNotification  $event
     * @return void
     */
    public function handle(TicketLog $event)
    {
        $data = [
            'ticket_id' => is_numeric($event->model->ticket_id) ? $event->model->ticket_id : $event->model->id,
            'message' => $event->data,
            'payload' => json_encode($event->model)
        ];

        $event->model->ticketLogs()->create($data);
    }
}
