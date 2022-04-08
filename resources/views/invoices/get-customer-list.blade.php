@if($records->count() > 0)
    @php $count = (($currentPage - 1) * $rowperpage); @endphp
    @foreach($records as $record)
        @php
            $customerQuote = $record->customerQuote;
            $count++;
            $data[] = [
                    $count,
                    $record->name ?? '--',
                    $record->invoice_number ?? '--',
                    !empty($record->formated_invoice_date) ? $record->formated_invoice_date : '--',
                    $customerQuote->quote_label ?? '--',
                    $record->amount_label ?? '--',
                    $record->amount_due ?? '--',
                    $record->payment_status ?? '--',
                    ($record->payment_status == config('constants.PAYMENT.PAID')) ?
                    (('<a href="'. route('tickets.invoices.view', ['id' => $customerQuote->ticket_id, 'invoice_id' => $record->id]). '" class="btn btn-sm btn-clean btn-icon view-icon" data-toggle="tooltip" data-theme="dark" title="View Details">
                        <i class="la la-eye"></i>
                    </a>')) : ''
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