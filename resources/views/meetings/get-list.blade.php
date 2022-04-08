@if($records->count() > 0)
    @php $count = (($currentPage - 1) * $rowperpage); @endphp
    @foreach($records as $record)
        @php
            $count++;
            $trainerUrl = !empty($record->trainer_id) ? route('trainer.view', ['id' => $record->trainer_id]) : 'javascript:void(0)';
            $customerUrl = !empty($record->customer_id) ? route('customer.view', ['id' => $record->customer_id]) : 'javascript:void(0)';
            $data[] = [
                    $count,
                    $record->admin_name ?? '--',
                    $record->customer_id ? '<a href="'.$customerUrl.'" target="_blank" data-toggle="tooltip" data-theme="dark" title="'.$record->customer_name.'">'.$record->customer_name.'</a>' : '--',
                    $record->trainer_id ? '<a href="'.$trainerUrl.'" target="_blank" data-toggle="tooltip" data-theme="dark" title="'.$record->trainer_name.'">'.$record->trainer_name.'</a>' : '--',
                    $record->meeting_title ?? '--',
                    $record->meeting_date_time ?? '--',
                    $record->timezone_label ?? '--',
                    $record->meeting_url ?? '--',
                    (!$isHistory) ?
                    (('<a href="'. route('tickets.meetings.cancel', ['id' => $record->ticket_id, 'meeting_id' => $record->id]). '" class="btn btn-sm btn-clean btn-danger cancel-meeting" data-toggle="tooltip" data-theme="dark" title="Cancel">
                        Cancel
                    </a>')) : $record->status ?? '--',
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