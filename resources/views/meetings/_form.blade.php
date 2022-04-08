<div class="card-body">
    <div class="form-group row">
        {{ Form::hidden('ticket_id', request()->route('id'), ['id' => 'ticket_id']) }}
        <div class="col-lg-6">
            {{ Form::label('create_meeting_with' , 'Create Meeting With <span style="color: red">*</span>','',false ) }}
            {{ Form::select('create_meeting_with', $meetingOptionList, null, ['class' => 'form-control select2', 'id' => 'create_meeting_with']) }}
            @error('create_meeting_with')
                @component('components.serverValidation')
                    {{ $message }}
                @endcomponent
            @enderror
        </div>
        <div class="col-lg-6">
            {{ Form::label('meeting_title', 'Meeting Title <span style="color: red">*</span>','',false ) }}
            {{ Form::text('meeting_title',null, ['class' => 'form-control', 'placeholder' => 'Enter meeting title']) }}
            @error('meeting_title')
                @component('components.serverValidation')
                    {{ $message }}
                @endcomponent
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <div class="col-lg-6">
			{{ Form::label('timezone_id', 'Timezone <span style="color: red">*</span>','',false ) }}
			{{ Form::select('timezone_id', array_column(App\Models\Timezone::getAllTimezone(), 'label','id'), null, ['class' => 'form-control select2', 'placeholder' => 'Select timezone', 'id' => 'timezone_id']) }}
			@error('timezone_id')
                @component('components.serverValidation')
                    {{ $message }}
                @endcomponent
			@enderror
		</div>
        <div class="col-lg-6">
            {{ Form::label('meeting_url', 'Meeting URL <span style="color: red">*</span>','',false ) }}
            {{ Form::text('meeting_url',null, ['class' => 'form-control', 'placeholder' => 'Enter meeting url']) }}
            @error('meeting_url')
                @component('components.serverValidation')
                    {{ $message }}
                @endcomponent
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <div class="col-lg-6">
            {{ Form::label('date', 'Date <span style="color: red">*</span>','',false ) }}
            {{ Form::text('date',null, ['class' => 'form-control', 'placeholder' => 'Select date', 'readonly' => "readonly", 'id' => 'date']) }}
            @error('date')
                @component('components.serverValidation')
                    {{ $message }}
                @endcomponent
            @enderror
		</div>
        <div class="col-lg-6">
            {{ Form::label('time', 'Time <span style="color: red">*</span>','',false ) }}
            {{ Form::text('time',null, ['class' => 'form-control', 'placeholder' => 'Select time', 'readonly' => "readonly", 'id' => 'time']) }}
            @error('time')
                @component('components.serverValidation')
                    {{ $message }}
                @endcomponent
            @enderror
        </div>
    </div>
    <div class="form-group row" id="interested_trainers" style="display:none;">
        <div class="col-lg-6">
            {{ Form::label('interested_trainer_id' , 'Interested trainer <span style="color: red">*</span>','',false ) }}
            {{ Form::select('interested_trainer_id', $interestedTrainers, null, ['class' => 'form-control select2', 'placeholder' => 'Select interested trainer', 'id' => 'interested_trainer_id']) }}
            @error('interested_trainer_id')
                @component('components.serverValidation')
                    {{ $message }}
                @endcomponent
            @enderror
        </div>
    </div>
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-lg-12 text-right">
            {{ Form::submit('Save', ['class' => 'btn btn-primary mr-2', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'title' => 'Save']) }}
            {{ Form::button('Reset', ['type' => 'reset','id' => 'reset','class' => 'btn btn-danger', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'title' => 'Reset']) }}
        </div>
    </div>
</div>

{{-- Style Section --}}
@section('styles')
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script>

</script>
<script src="{{ asset('js/pages/crud/forms/widgets/bootstrap-timepicker.js') }}"></script>
<script src="{{ asset('js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/custome-validation/additional-methods.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/custome-validation/meeting.js') }}" type="text/javascript"></script>

<script>
    /** Apply select2 */
    $(".select2").select2({
        width: '100%'
    });
    var date = new Date();
    var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    var time = date.getHours() + ":" + date.getMinutes();
    $('#date').datepicker({
        format: "yyyy-mm-dd",
        todayHighlight: true,
        startDate: today,
        autoclose: true
    });
    $('#time').timepicker({
        minuteStep: 1,
        defaultTime: '',
        showSeconds: true,
        showMeridian: false,
    }).on('change', function(e) {
        var time = $(this).val();
        var arr = time.split(':');
        if (arr[0].length == 1) {
            time = '0' + arr[0] + ':' + arr[1] + ':' + arr[2];
        }
        $('#time').val(time);
    });

    $(document).on('change', '#create_meeting_with', function(e) {
        if($.inArray($(this).val(), ["{{config('constants.MEETING.CREATE_WITH.INTERESTED_TRAINER')}}", "{{config('constants.MEETING.CREATE_WITH.CUSTOMER_AND_INTERESTED_TRAINER')}}"]) !== -1) {
            $('#interested_trainers').show();
        } else {
            $('#interested_trainer_id').valid();
            $('#interested_trainers').hide();
        }
    });
</script>
@endsection