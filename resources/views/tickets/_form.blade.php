@csrf
<div class="modal-body">
    <h2 class="text-center font-size-lg">Ticket ID - {{$ticket->ticket_id ?? null}}</h2>
    <div class="card-body">
        {{ Form::hidden('ticket_id', $ticket->id ?? null, ['id' => 'ticket_id']) }}
        @if(\Auth::user()->role_id == config('constants.ADMIN_ROLE.SUPER_ADMIN'))
        <div class="form-group row">
            <div class="col-lg-12">
                {{ Form::label('user_id', 'Sub admin <span style="color: red">*</span>','',false ) }}
                {{ Form::select('user_id', $users, $ticket->user_id ?? null, ['class' => 'form-control sub-admin subadmin-group', 'placeholder' => 'Select Sub admin', 'id' => 'sub-admin-dropdown', (isset($ticket->user_id) && $ticket->user_id == \Auth::id()) ? 'disabled' : '']) }}
                @error('user_id')
                    @component('components.serverValidation')
                        {{ $message }}
                    @endcomponent
                @enderror
            </div>
        </div>
        <h3 class="text-center font-size-lg mb-6">OR</h3>
        @endif
        <div class="form-group row">
            <div class="col-lg-12">
                {{ Form::label('assign_to_self', 'Do you want to assign this ticket to yourself?') }}
                <div class="checkbox-inline">
                    <label class="checkbox">
                        <input type="checkbox" class="subadmin-group" id="assign_to_self" name="assign_to_self" value="1" {{ (isset($ticket) && $ticket->user_id == \Auth::id() ) ? "checked" : "" }} @if(\Auth::user()->role_id == config('constants.ADMIN_ROLE.SUPER_ADMIN')) {{ (!empty($ticket->user_id) && $ticket->user_id != \Auth::id() ) ? "disabled" : "" }} @endif>
                        <span></span>{{ __('Yes') }}</label>
                </div>
                @error('assign_to_self')
                    @component('components.serverValidation')
                        {{ $message }}
                    @endcomponent
                @enderror
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    {{ Form::submit(__('Assign'), ['class' => 'btn btn-primary mr-2', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'title' => __('Assign')]) }}
    {{ Form::button(__('Reset'), ['type' => 'reset', 'id' => 'reset','class' => 'btn btn-danger', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'title' => __('Reset')]) }}
</div>
