@if($records->count() > 0)
    @php $priceKey = CommonHelper::getTrainerTicketTypeKey()[$ticket->ticket_type_id] ?? ''; @endphp
    @foreach($records as $record)
        @php
            $data[] = [
                    (('<input type="checkbox" value="'.$record->id.'">')),
                    $record->full_name ?? '--',
                    ($priceKey) ? $record->$priceKey ?? '--' : '--',
                    $record->skill_title ?? '--',
                    ($record->primarySkills->count() > 0) ? $record->primarySkills->implode('name', ', ') : '--',
                    $record->courseCategory->name ?? '--',
                    $record->total_experience ?? '--',
                ];
        @endphp
    @endforeach
@else
    @php
        $data = [];
    @endphp
@endif

@php
    $response = [
        "draw" => intval($draw),
        "iTotalRecords" => $rowperpage,
        "iTotalDisplayRecords" => $records->total(),
        "aaData" => $data
    ];

    echo json_encode($response);
@endphp