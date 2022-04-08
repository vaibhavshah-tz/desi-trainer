{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('tickets-edit-title', $ticketDetails) }}
@endsection

{{-- Content --}}
@section('content')
<!--begin::Card-->
<div class="card card-custom">
<div class="card-header">
        @component('components.setActionBtn')
        @slot('title')
        Ticket Status and Admin Details
        @endslot
        @slot('viewRoute')
        {{ route('tickets.view', $ticketDetails->id) }}
        @endslot
        @endcomponent
    </div>
    {{ Form::model($ticketDetails, ['route' => ['tickets.update', $ticketDetails->id], 'method' => 'patch','id' => 'ticket-basic-info','class' => 'form', 'enctype' => 'multipart/form-data']) }}
    <div class="card-body">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="row">
            <div class="col-lg-6">
                {{ Form::label('status', 'Ticket Status <span style="color: red">*</span>','',false ) }}
                {{ Form::select('status', $ticketStatus, null, ['class' => 'form-control has-error select2', 'id' => 'status']) }}
                @error('status')
                @component('components.serverValidation')
                {{ $message }}
                @endcomponent
                @enderror
            </div>
            <div class="col-lg-6">
                {{ Form::label('user_id', 'Admin Info <span style="color: red">*</span>','',false ) }}
                {{ Form::select('user_id', $users, $ticketDetails->user_id ?? null, ['class' => 'form-control sub-admin subadmin-group select2', 'placeholder' => 'Select Admin Info', 'id' => 'sub-admin-dropdown']) }}
                @error('user_id')
                @component('components.serverValidation')
                {{ $message }}
                @endcomponent
                @enderror
            </div>
        </div>
    </div>
    <div class="card-header">
        @component('components.setActionBtn')
        @slot('title')
        Ticket Details
        @endslot
        @endcomponent
    </div>
    <div class="card-body">
        <div class="form-group row">
            <div class="col-lg-6">
                {{ Form::label('ticket_type_id', 'Ticket Type <span style="color: red">*</span>','',false ) }}
                {{ Form::select('ticket_type_id', array_column(App\Models\TicketType::getAllTicketType(), 'name','id'), null, ['class' => 'form-control select2', 'id' => 'ticket_type']) }}
                @error('ticket_type_id')
                @component('components.serverValidation')
                {{ $message }}
                @endcomponent
                @enderror
            </div>
            <div class="col-lg-6">
                {{ Form::label('course_category_id', 'Course Category','',false ) }}
                {{ Form::select('course_category_id', $allCourseCategory, $ticketDetails->course_category_id, ['class' => 'form-control has-error select2', 'placeholder' => 'Select course category', 'id' => 'courseCategory']) }}
                @error('course_category_id')
                @component('components.serverValidation')
                {{ $message }}
                @endcomponent
                @enderror
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-6">
                {{ Form::label('course_id', 'Course Name','',false ) }}
                {{ Form::select('course_id', $getCourse, $ticketDetails->course_id, ['class' => 'form-control has-error select2', 'placeholder' => 'Select course name', 'id' => 'course_id']) }}
                @error('course_id')
                @component('components.serverValidation')
                {{ $message }}
                @endcomponent
                @enderror
            </div>
            <div class="col-lg-6">
                {{ Form::label('primary_skill', 'Primary Skills','',false ) }}
                {{ Form::select('primary_skill[]', array_column($primarySkills, 'name','id'), isset($ticketDetails) ? $ticketDetails->primarySkills : null, ['class' => 'form-control primary_skill select2 primary-select', 'id' => 'primary_skill', 'multiple' => 'multiple']) }}
                @error('primary_skill')
                @component('components.serverValidation')
                {{ $message }}
                @endcomponent
                @enderror
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-6">
                {{ Form::label('other_course_category', 'Other Course Category') }}
                {{ Form::text('other_course_category',null, ['class' => 'form-control', 'placeholder' => 'Enter other course category']) }}
                @error('other_course_category')
                @component('components.serverValidation')
                {{ $message }}
                @endcomponent
                @enderror
            </div>
            <div class="col-lg-6">
                {{ Form::label('other_course', 'Other Course Name') }}
                {{ Form::text('other_course',null, ['class' => 'form-control', 'placeholder' => 'Enter other course name']) }}
                @error('other_course')
                @component('components.serverValidation')
                {{ $message }}
                @endcomponent
                @enderror
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-6">
                {{ Form::label('other_primary_skill', 'Other Primary Skills') }}
                {{ Form::text('other_primary_skill', null, ['class' => 'form-control', 'placeholder' => 'Enter other primary skills']) }}
                @error('other_primary_skill')
                @component('components.serverValidation')
                {{ $message }}
                @endcomponent
                @enderror
            </div>
            <div class="col-lg-6">
                {{ Form::label('date', 'Ticket Date <span style="color: red">*</span>','',false ) }}
                <div class="input-group input-group-solid date" data-target-input="nearest">
                    {{ Form::text('dates', $ticketDetails->date, ['class' => 'form-control form-control-solid datetimepicker-input', 'placeholder' => 'Enter select date', 'id' => 'date']) }}
                    <!-- <input name="date" type="text" value="{{ $ticketDetails->date }}" class="form-control form-control-solid datetimepicker-input" placeholder="Select date &amp; time" data-target="#kt_datetimepicker_3" /> -->
                    <div class="input-group-append" data-toggle="datepicker">
                        <span class="input-group-text">
                            <i class="ki ki-calendar"></i>
                        </span>
                    </div>
                </div>
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
                {{ Form::label('time', 'Ticket Time <span style="color: red">*</span>','',false ) }}
                <div class="input-group timepicker">
                    <input name="time" value="{{ $ticketDetails->time ?? '' }}" class="form-control" id="timepicker" readonly="readonly" placeholder="Select time" type="text" />
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="la la-clock-o"></i>
                        </span>
                    </div>
                </div>
            </div>
            <input type="hidden" name="date" value="{{ $ticketDetails->date }}" id="setDate">
        </div>
        <div class="form-group row">
            <div class="col-lg-6">
                {{ Form::label('customer_id', 'Customer Name') }}
                {{ Form::text('customer_id',$ticketDetails->customer->full_name ?? '', ['class' => 'form-control', 'placeholder' => 'Enter customer name', 'disabled']) }}
                @error('customer_id')
                @component('components.serverValidation')
                {{ $message }}
                @endcomponent
                @enderror
            </div>
            <div class="col-lg-6">
                {{ Form::label('customer_email', 'Customer Email') }}
                {{ Form::text('customer_email',$ticketDetails->customer->email ?? '', ['class' => 'form-control', 'placeholder' => 'Enter customer name', 'disabled']) }}
                @error('customer_email')
                @component('components.serverValidation')
                {{ $message }}
                @endcomponent
                @enderror
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-12">
                {{ Form::label('message', 'Message added by customer') }}
                {{ Form::textarea('message',null, ['class' => 'form-control commentBox','cols'=>'5','rows'=>'5', 'placeholder' => 'Enter comments']) }}
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                {{ Form::label('is_global', 'Mark ticket as global') }}
                <div class="checkbox-inline">
                    <label class="checkbox">
                        <input type="checkbox" name="is_global" value="1" {{ (isset($ticketDetails) && $ticketDetails->is_global )? "checked" : "" }}>
                        <span></span>{{ __('Yes') }}</label>
                </div>
                @error('is_global')
                @component('components.serverValidation')
                {{ $message }}
                @endcomponent
                @enderror
            </div>
        </div>
        <!-- <div class="bg-gray-200 d-flex flex-center h-100px mb-1 mb-md-0 rounded">
            <h3 class="card-title">Connect With Customer</h3>
            <button type="button" class="btn btn-clean btn-info call-btn ml-5" disabled data-id="{{ $ticketDetails->customer_id }}" data-type="1" data-toggle="tooltip" data-theme="dark" title="Call">
                Call
            </button>
        </div> -->
    </div>
    @if($ticketDetails->is_for_employee === 1)
    <div class="card-header">
        @component('components.setActionBtn')
        @slot('title')
        Employee Details
        @endslot
        @endcomponent
    </div>
    <div class="card-body">
        @if($ticketDetails->ticketEmployees->count() == 0)
        <p class="bg-secondary py-2 px-4">{{ "Employee Details not found" }}</p>
        @endif
        @foreach($ticketDetails->ticketEmployees as $key => $value)
        <div class="form-group row">
            <div class="col-lg-6">
                {{ Form::label('employee_name' , 'Employee Name') }}
                {{ Form::text('employee_name', $value->employee_name ?? '', ['class' => 'form-control', 'placeholder' => 'Enter employee name', 'disabled']) }}
                @error('employee_name')
                @component('components.serverValidation')
                {{ $message }}
                @endcomponent
                @enderror
            </div>
            <div class="col-lg-6">
                {{ Form::label('email', 'Employee email') }}
                {{ Form::email('email', $value->email ?? '', ['class' => 'form-control', 'placeholder' => 'Enter email', 'disabled']) }}
                @error('email')
                @component('components.serverValidation')
                {{ $message }}
                @endcomponent
                @enderror
            </div>
        </div>
        <div class="form-group row">
            @php $fullPhoneNumber = !empty(old('phone_number')) ? old('country_code').old('phone_number') : $user->full_phone_number ?? null @endphp
            {{ Form::hidden('country_code', null, ['id' => 'country_code']) }}
            <div class="col-lg-6">
                {{ Form::label('phone_number', 'Phone Number') }}
                {{ Form::text('phone_number', $value->full_phone_number ?? '', ['class' => 'form-control', 'placeholder' => 'Enter phone number', 'id' => 'phone_number', 'disabled']) }}
                @error('phone_number')
                @component('components.serverValidation')
                {{ $message }}
                @endcomponent
                @enderror

                @error('full_phone_number')
                @component('components.serverValidation')
                {{ $message }}
                @endcomponent
                @enderror
            </div>
        </div>
        <hr>
        @endforeach
        <!-- <div class="row">
            <div class="col-lg-12 text-right">
                {{ Form::button('Add employees', ['class' => 'btn btn-primary mr-2', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'title' => 'Add employees', 'id' => 'addE']) }}
            </div>
        </div> -->
    </div>

    <!-- <input type="hidden" name="is_for_employee" value="1"> -->
    @endif
    <div class="card-header">
        @component('components.setActionBtn')
        @slot('title')
        Customer Budget
        @endslot
        @endcomponent
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-6">
                {{ Form::label('customer_budget', 'Budget (INR) <span style="color: red">*</span>','',false ) }}
                {{ Form::number('customer_budget', null, ['min' => '0','class' => 'form-control', 'placeholder' => 'Enter customer budget']) }}
                @error('customer_budget')
                @component('components.serverValidation')
                {{ $message }}
                @endcomponent
                @enderror
            </div>
        </div>
    </div>

    <div class="card-header">
        @component('components.setActionBtn')
        @slot('title')
        Assigned Trainer
        @endslot
        @endcomponent
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-5">
                {{ Form::label('traine_name', 'Assigned Trainer') }}
                {{ Form::text('traine_name', $ticketDetails->trainer->full_name ?? '', ['class' => 'form-control', 'placeholder' => 'Assigned trainer', 'disabled']) }}
                @error('traine_name')
                @component('components.serverValidation')
                {{ $message }}
                @endcomponent
                @enderror
            </div>
            <div class="col-lg-3">
                {{ Form::label('currency', 'Currency') }}
                {{ Form::select('currency', CommonHelper::getCurrency(), !empty($ticketDetails->trainer->quotes) ? @$ticketDetails->trainer->quotes->first()->currency : '', ['class' => 'form-control select2', 'placeholder' => 'Select currency', 'id' => 'currency', 'disabled']) }}
                @error('timezone_id')
                @component('components.serverValidation')
                {{ $message }}
                @endcomponent
                @enderror
            </div>
            <div class="col-lg-4">
                {{ Form::label('final_pricing', 'Final Pricing') }}
                {{ Form::text('final_pricing', !empty($ticketDetails->trainer->quotes) ? @$ticketDetails->trainer->quotes->first()->quote : '', ['class' => 'form-control', 'placeholder' => 'Final pricing', 'disabled']) }}
                @error('final_pricing')
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


    {{ Form::close() }}
</div>
<!--end::Card-->
@endsection

{{-- Style Section --}}
@section('styles')
<link href="{{ asset('css/intlTelInput.css') }}" rel="stylesheet" type="text/css" />
<style>
    .card-title {
        margin-bottom: 0rem !important;
    }

    .iti {
        display: inherit;
    }

    .iti__flag {
        background-image: url("{{ asset('media/flags.png') }}");
    }

    @media (-webkit-min-device-pixel-ratio: 2),
    (min-resolution: 192dpi) {
        .iti__flag {
            background-image: url("{{ asset('media/flags@2x.png') }}");
        }
    }
</style>
@endsection

{{-- Scripts Section --}}
@section('scripts')
<!-- <script src="{{ asset('plugins/custom/nexmo-client/nexmoClient.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/call.js') }}" type="text/javascript"></script> -->
<script src="{{ asset('js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/custome-validation/additional-methods.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/custome-validation/ticket-basic-info.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/intlTelInput.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    var $dates = $('#date').datepicker();
    var ticketDate = "{{ $ticketDetails->date }}";
    var ticketTime = "{{ $ticketDetails->time }}";
    var date = new Date();
    var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    var ticketSelectedDate = new Date(ticketDate);

    /** Apply select2 */
    $(".select2").select2({
        width: '100%'
    });
    $(".primary_skill").select2({
        placeholder: "Select primary skill",
        allowClear: true,
        width: '100%'
    });
    $(".ticket_type").select2({
        placeholder: "Select tickt type",
        allowClear: true,
        width: '100%'
    });

    /**
     * set the date value
     */
    $dates.datepicker({
        format: "yyyy-mm-dd",
        todayHighlight: true,
        startDate: today,
        autoclose: true,
    });
    $('#date').datepicker('setDate', ticketSelectedDate);
    $('#date').on('changeDate', function(e) {
        var date = e.date;
        var day = ('0' + date.getDate()).slice(-2);
        var month = ('0' + (date.getMonth() + 1)).slice(-2);
        var year = date.getFullYear();
        $("#setDate").val(year + '-' + month + '-' + day);
    });

    /**
     * set the time value
     */
    $('#timepicker').timepicker({
        minuteStep: 1,
        defaultTime: '',
        showSeconds: true,
        showMeridian: false,
    });
    $('#timepicker').val(ticketTime);
    $('#timepicker').on('change', function(e) {
        var time = $(this).val();
        var arr = time.split(':');
        if (arr[0].length == 1) {
            time = '0' + arr[0] + ':' + arr[1] + ':' + arr[2];
        }
        $('#timepicker').val(time);
    });

    /** Reset the select2 value */
    $(document).on("click", "#reset", function() {
        $('.select2').val('').trigger('change');
        $dates.datepicker('setDate', null);
        $("#date").val("");
        $("#message").html("");
    });

    // createPhoneNumberInput('#phone_number'); // Pass id of the input

    /**
     * Set the course name based on course category value
     */
    $(document).on("change", "#courseCategory", function(e) {
        $.ajax({
            type: 'GET',
            url: "{{ route('categorywise.course') }}",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: {
                course_category_id: $(this).val(),
            },
            success: function(data) {
                var $newOption = '';
                if (data.length > 0) {
                    $("#course_id").empty();
                    $newOption += '<option value="">Select course name</option>';
                    $.each(data, function(index, value) {
                        $newOption += '<option value="' + value.id + '">' + value.name + '</option>';
                    });
                    $("#course_id").append($newOption).trigger('change');
                }
            },
            error: function(data) {
                toastr.error("Course has not been not set.");
            }
        });

        $.ajax({
            type: 'GET',
            url: "{{ route('categorywise.skills') }}",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: {
                course_category_id: $(this).val(),
            },
            success: function(data) {
                var $newOption = '';
                $("#primary_skill").empty();
                // $newOption += '<option value="">Select primary skills</option>';
                if (data.length > 0) {
                    if (data.length > 0) {
                        $.each(data, function(index, value) {
                            $newOption += '<option value="' + value.id + '">' + value.name + '</option>';
                        });
                    }
                }
                $("#primary_skill").append($newOption).trigger('change');
            },
            error: function(data) {
                toastr.error("Primary skill has not been not set.");
            }
        });
    });
</script>
@endsection