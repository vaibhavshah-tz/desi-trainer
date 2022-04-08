<div class="table-responsive">
    <table class="table table-head-custom table-head-bg table-borderless table-vertical-center">
        <thead>
            <tr class="text-uppercase">
                <th style="min-width: 250px" class="pl-7">
                    <span class="text-dark-75">Name</span>
                </th>
                <th style="min-width: 150px">
                    <span class="text-dark-75">Category</span>
                </th>
                <th style="min-width: 150px">
                    <span class="text-dark-75">Primary Skills</span>
                </th>
                <th style="min-width: 100px">
                    <span class="text-dark-75">Total Exp.</span>
                </th>
                <th style="min-width: 100px">
                    <span class="text-dark-75">Status</span>
                </th>
                <th style="min-width: 150px">
                    <span class="text-dark-75">Reason</span>
                </th>
                <th style="min-width: 50px">
                    <span class="text-dark-75">Action</span>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($proposalTrainers as $trainer)
            @if($trainer->trainer)
            <tr class="trainer-list">
                <td class="pl-0 py-8">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-50 flex-shrink-0 mr-4">
                            <div class="symbol-label" style="background-image: url({{asset($trainer->trainer->avatar_url)}})"></div>
                        </div>
                        <div>
                            <a href="{{ route('trainer.view', $trainer->trainer->id) }}" class=" text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">{{ $trainer->trainer->full_name ?? config('constants.DEFAULT_MSG') }}</a>
                            <span class="text-muted font-weight-bold d-block">{{ $trainer->trainer->skill_title ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="d-block font-size-lg">{{ $trainer->trainer->courseCategory->name ?? config('constants.DEFAULT_MSG')  }}</span>
                </td>
                <td>
                    <span class="d-block font-size-lg">
                        @if($trainer->trainer->primarySkills->count() > 0)
                        {{ $trainer->trainer->primarySkills->implode('name', ', ') }}
                        @else
                        {{ config('constants.DEFAULT_MSG') }}
                        @endif
                    </span>
                </td>
                <td>
                    <span class="d-block font-size-lg">{{ $trainer->trainer->total_experience ?? config('constants.DEFAULT_MSG') }}</span>
                </td>
                <td>
                    <span class="label label-lg label-inline {{CommonHelper::proposalStatusLabel($trainer->action)['class']}}">{{CommonHelper::proposalStatusLabel($trainer->action)['title']}}</span>
                </td>
                <td class="text-left pr-0">
                    {{ $trainer->denied_reason ?? config('constants.DEFAULT_MSG') }}
                </td>
                <td class="pr-0">
                    <button data-link="{{route('tickets.proposals.assign-trainer', ['id' => $proposal->ticket_id, 'proposal_id' => $trainer->proposal_id, 'trainer_id' => $trainer->trainer->id])}}" class="btn btn-sm btn-primary assign-trainer mb-1" {{ ($trainer->action != config('constants.PROPOSAL.ACCEPTED') || $proposal->is_assigned == 1 || $ticket->customer_has_made_payment == 0) ? 'disabled' : '' }} data-name="{{ $trainer->trainer->full_name }}" data-toggle="tooltip" data-theme="dark" title="Assign Trainer">
                        Assign
                    </button>
                    <button class="btn btn-sm btn-clean btn-info call-btn mb-1" disabled data-id="{{$trainer->trainer->id}}" data-type="2" data-toggle="tooltip" data-theme="dark" title="Call">
                        Call
                    </button>
                    <button class="btn btn-sm btn-clean btn-info call-btn" disabled data-id="{{$proposal->ticket_id}}" data-type="4" data-tid="{{$trainer->trainer->id}}" data-toggle="tooltip" data-theme="dark" title="Conference Call">
                        Conference Call
                    </button>
                </td>
            </tr>
            @endif
            @empty
            <tr class="text-center pr-0 trainer-list">
                <td class="pl-0 py-8" colspan="7">{{ __('No trainers found') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="row">
    <div class="col-lg-6 text-left">
        <div>Showing {{ ($proposalTrainers->count() > 0) ? ($proposalTrainers->currentpage()-1)*$proposalTrainers->perpage()+1 : 0}} to {{($proposalTrainers->currentpage()-1) * $proposalTrainers->perpage() + $proposalTrainers->count()}}
            of {{$proposalTrainers->total()}} entries
        </div>
    </div>
    <div class="col-lg-6 pull-right">
        {{ $proposalTrainers->links() }}
    </div>
</div>