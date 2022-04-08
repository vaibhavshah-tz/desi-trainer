@if($records->count() > 0)
    @php $count = (($currentPage - 1) * $rowperpage); $url = 'javascript:void(0)'; @endphp
    @foreach($records as $record)
        @php
            $count++;
            $url = url("tickets/$ticketId/chat/$record->id#pills-itrainer");            
            $trainerUrl = route('trainer.view', ['id' => $record->id]);
            $data[] = [
                    $count,
                    '<a href="'.$trainerUrl.'" data-toggle="tooltip" target="_blank" data-theme="dark" title="'.$record->full_name.'">'.$record->full_name.'</a>',
                    $record->email ?? '--',
                    $record->skill_title ?? '--',                    
                    ($record->primarySkills->count() > 0) ? $record->primarySkills : [],
                    $record->total_experience ?? '--',
                    (('<button class="btn btn-sm btn-clean btn-info call-btn mr-1" disabled data-id="'.$record->id.'" data-type="2" data-toggle="tooltip" data-theme="dark" title="Call">
                        Call
                    </button>').
                    ('<a href="'.$url.'" class="btn btn-sm btn-clean btn-primary call-btn mr-1" data-toggle="tooltip" data-theme="dark" title="Chat">
                        Chat
                    </a>').
                    ('<button class="btn btn-sm btn-clean btn-info call-btn mr-1" disabled data-id="'.$ticketId.'" data-type="4" data-tid="'.$record->id.'" data-toggle="tooltip" data-theme="dark" title="Conference Call">
                        Conference Call
                    </button>'))
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