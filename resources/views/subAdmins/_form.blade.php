@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<div class="card-body">
    <div class="form-group row">
        <div class="col-lg-6">
            {{ Form::label('first_name' , 'First name <span style="color: red">*</span>','',false ) }}
            {{ Form::text('first_name',null, ['class' => 'form-control', 'placeholder' => 'Enter first name']) }}
            @error('first_name')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
        <div class="col-lg-6">
            {{ Form::label('last_name', 'Last name <span style="color: red">*</span>','',false ) }}
            {{ Form::text('last_name',null, ['class' => 'form-control', 'placeholder' => 'Enter last name']) }}
            @error('last_name')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <div class="col-lg-6">
            {{ Form::label('email', 'Email <span style="color: red">*</span>','',false ) }}
            {{ Form::email('email',null, ['class' => 'form-control', 'placeholder' => 'Enter email', 'id' => 'user-email']) }}
            @error('email')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
        @php $fullPhoneNumber = !empty(old('phone_number')) ? old('country_code').old('phone_number') : $user->full_phone_number ?? null @endphp
        {{ Form::hidden('country_code', null, ['id' => 'country_code']) }}
        <div class="col-lg-6">
            {{ Form::label('phone_number', 'Contact number <span style="color: red">*</span>','',false ) }}
            {{ Form::text('phone_number', $fullPhoneNumber, ['class' => 'form-control', 'placeholder' => 'Enter contact number', 'id' => 'phone_number']) }}
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
    @if(!isset($user->id))
    <div class="form-group row">
        <div class="col-lg-6">
            {{ Form::label('password', 'Password <span style="color: red">*</span>','',false ) }}
            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" value="" placeholder="Enter password" autocomplete="false" />
            @error('password')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
        <div class="col-lg-6">
            {{ Form::label('password_confirmation', 'Confirm password <span style="color: red">*</span>','',false ) }}
            <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" value="" placeholder="Enter confirm password" autocomplete="false" />
            @error('password_confirmation')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
    </div>
    @endif
    <div class="form-group row">
        <div class="col-lg-6">
            {{ Form::label('status', 'Status') }}
            {{ Form::select('status', CommonHelper::getStatus(), null, ['class' => 'form-control kt-select2 select2 '.($errors->has('status') ? 'is-invalid' : ''), 'id' => 'kt_select2_1']) }}
            @error('status')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
        <div class="col-lg-6">
            {{ Form::label('call_support', 'Do you want to assign on call support to this sub admin?') }}
            <div class="checkbox-inline">
                <label class="checkbox">
                    <input type="checkbox" name="call_support" value="1" {{ (isset($user) && $user->call_support )? "checked" : "" }}>
                    <span></span>{{ __('Yes') }}</label>
            </div>
            @error('call_support')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>

        <!-- <label class="col-xl-2 col-lg-1 col-form-label">{{ __('Avatar') }}</label>
        <div class="col-lg-4">
            <div class="image-input image-input-outline" id="kt_profile_avatar" style="background-image: url({{ asset(config('constants.IMAGE_PATH.DEFAULT_AVATAR')) }})">
                <div class="image-input-wrapper" style="background-image: url({{ asset($user->avatar_url ?? '') }})"></div>
                <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="{{ __('Change avatar') }}">
                    <i class="fa fa-pen icon-sm text-muted"></i>
                    <input type="file" name="avatar" id="avatar" accept=".png, .jpg, .jpeg" />
                    <input type="hidden" name="avatar_remove" />
                </label>
                <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="{{ __('Cancel avatar') }}">
                    <i class="ki ki-bold-close icon-xs text-muted"></i>
                </span>
            </div>
            <span class="form-text text-muted">{{ __('Allowed file types: png, jpg, jpeg.') }}</span>
            @error('avatar')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div> -->
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
<link href="{{ asset('css/intlTelInput.css') }}" rel="stylesheet" type="text/css" />
<style>
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
<script src="{{ asset('js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/custome-validation/additional-methods.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/custome-validation/sub-admin.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/intlTelInput.js') }}" type="text/javascript"></script>
<script>
    // var avatar = new KTImageInput('kt_profile_avatar');
    // avatar.on('remove', function(imageInput) {
    //     $('input[type=hidden][name=avatar_remove]').val('1');
    // });
    /** Apply select2 */
    $(".select2").select2({
        width: '100%'
    });
    createPhoneNumberInput('#phone_number'); // Pass id of the input

    /** Reset the select2 value */
    $(document).on("click", "#reset", function() {
        // $('.select2').val('').trigger('change');
        $(".image-input-wrapper").css({
            "background-image": ""
        });
        // $("#avatar").val('');
        // image-input-wrapper
    });

    $(document).on('change', '[name="call_support"]', function(event) {
        if ($(this).is(':checked')) {
            $.ajax({
                method: 'POST',
                data: {
                    id: $('#user-id').val()
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/check-call-support",
                success: function(data, status, xhr) {
                    if (xhr.status == 200 && data.full_name != '' && data.full_name != undefined) {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: data.full_name + " is already assigned, Only one sub admin can be assigned for call support. By confirming, Previously assigned sub admin will be removed from call support.",
                            icon: "warning",
                            allowOutsideClick: false,
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: "Yes",
                        }).then(function(data) {
                            if (data.isDismissed) {
                                $(event.target).prop('checked', false);
                            }
                        });
                    }
                },
                error: function(data) {
                    $(event.target).prop('checked', false);
                },
            })
        }
    });
</script>
@endsection