<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BottomNavigationCountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'proposal_count' => $this->proposal_count,
            'interested_ticket_count' => $this->interested_ticket_count,
            'assign_ticket_count' => $this->assign_ticket_count,
            'global_request_count' => $this->global_request_count,
        ];
    }
}
