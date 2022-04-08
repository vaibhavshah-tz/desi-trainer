@if($records->count() > 0)
    @php $count = (($currentPage - 1) * $rowperpage); @endphp
    @foreach($records as $record)
        @php
            $trainerQuote = $record->trainerQuote;
            $count++;
            $data[] = [
                    $count,
                    !empty($record->file_url) ?
                    ('<a href="'. $record->file_url . '" target="_blank" class="symbol symbol-50 flex-shrink-0 mr-4">
                        <div class="symbol-label" style="background-image: url('. $record->file_url . ')"></div>
                    </a>') : '--',
                    $record->invoice_number ?? '--',
                    !empty($record->formated_invoice_date) ? $record->formated_invoice_date : '--',
                    $trainerQuote->quote_label ?? '--',
                    $record->amount_label ?? '--',
                    $record->amount_due ?? '--',
                    $record->payment_status ?? '--',
                    (('<button data-url="'. route('tickets.trainer-invoices.edit', ['id' => $trainerQuote->ticket_id, 'invoice_id' => $record->id]). '" class="btn btn-sm btn-clean btn-icon invoice-modal edit-icon" data-toggle="tooltip" data-theme="dark" title="Edit">
                        <i class="la la-edit"></i>
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