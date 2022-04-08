@if($records->count() > 0)
    @php $count = (($currentPage - 1) * $rowperpage); @endphp
    @foreach($records as $record)
        <?php
            $trainers = '';
            $trainers .= '<div class="text-center">';
            $trainers .= '<div class="symbol-group symbol-hover justify-content-center">';
            if(count($record->trainers) > 0) {
                $collection = $record->trainers;
                $chunk = $collection->splice(2);
                foreach($collection as $trainer) {
                    $trainers .= '<a href="'.route('trainer.view', $trainer->trainer->id).'" class="symbol symbol-30 symbol-circle mr-3" data-toggle="tooltip" data-theme="dark" title="'. $trainer->trainer->full_name .'">';
                    $trainers .= '<img alt="Avatar" src="'. asset($trainer->trainer->avatar_url) .'">';
                    $trainers .= '</a>';
                }
                if($chunk->count() > 0) {
                    $trainers .= '<div class="symbol symbol-30 symbol-circle symbol-light-info mr-3" data-toggle="tooltip" title="" data-original-title="Invite someone">';
                    $trainers .= '<span class="symbol-label font-weight-bold">+'.$chunk->count().'</span>';
                    $trainers .= '</div>';
                }
            } else {
                $trainers .= '<span>--</span>';
            }
            $trainers .= '</div>';
            $trainers .= '</div>';
            $count++;
            $data[] = [
                    $count,
                    $record->name ?? '--',
                    $record->quote_label ?? '--',
                    $trainers,
                    (('<a href="'. route('tickets.proposals.view', ['id' => $record->ticket_id, 'proposal_id' => $record->id]) .'" class="btn btn-sm btn-clean btn-icon view-icon" data-toggle="tooltip" data-theme="dark" title="View details">
                        <i class="la la-eye"></i>
                    </a>').
                    ('<a href="'. route('tickets.proposals.edit', ['id' => $record->ticket_id, 'proposal_id' => $record->id]) .'" class="btn btn-sm btn-clean btn-icon edit-icon" data-toggle="tooltip" data-theme="dark" title="Edit">
                        <i class="la la-edit"></i>
                    </a>').
                    ('<a href="'. route('tickets.proposals.delete', ['id' => $record->ticket_id, 'proposal_id' => $record->id]) .'" class="btn btn-sm btn-clean btn-icon delete-record" data-toggle="tooltip" data-theme="dark" title="Delete">
                        <i class="la la-trash"></i>
                    </a>'))
                ];
        ?>
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