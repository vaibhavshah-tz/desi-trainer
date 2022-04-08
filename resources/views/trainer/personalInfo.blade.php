<!--begin: Wizard Step 1-->
<div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
    <h4 class="mb-10 font-weight-bold text-dark">{{ __('Setup Your Personal Info') }}</h4>
    <div class="form-group row">
        {{ Form::hidden('user_id', $trainer->id ?? 0, ['id' => 'user-id']) }}
        <div class="col-lg-6">
            {{ Form::label('first_name', 'First name <span style="color: red">*</span>','',false ) }}
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
            {{ Form::email('email',null, ['class' => 'form-control', 'placeholder' => 'Enter email']) }}
            @error('email')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
        <!-- <div class="col-lg-6">
            {{ Form::label('username', 'Username <span style="color: red">*</span>','',false ) }}
            {{ Form::text('username',null, ['class' => 'form-control', 'placeholder' => 'Enter username']) }}
            @error('username')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div> -->
        <div class="col-lg-6">
            {{ Form::label('status', 'Status') }}
            {{ Form::select('status', CommonHelper::getTrainerStatus(), null, ['class' => 'form-control kt-select2 select2', 'id' => 'kt_select2_1']) }}
            @error('status')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <div class="col-lg-6">
            <label>{{ __('Gender') }}</label>
            <div class="radio-inline">
                <label class="radio radio-lg">
                    <input type="radio" {{ (isset($trainer) && $trainer->gender=="1")? "checked" : "checked" }} name="gender" value="1" />
                    <span></span>{{ __('Male') }}</label>
                <label class="radio radio-lg">
                    <input type="radio" {{ (isset($trainer) && $trainer->gender=="2")? "checked" : "" }} name="gender" value="2" />
                    <span></span>{{ __('Female') }}</label>
            </div>
            @error('gender')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
        <label class="col-xl-2 col-lg-1 col-form-label">{{ __('Avatar') }}</label>
        <div class="col-lg-4">
            <div class="image-input image-input-outline" id="kt_profile_avatar" style="background-image: url({{ asset(config('constants.IMAGE_PATH.DEFAULT_AVATAR')) }})">
                <div class="image-input-wrapper" style="background-image: url({{ asset($trainer->avatar_url ?? '') }})"></div>
                <!-- <div class="image-input-wrapper"></div> -->
                <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar">
                    <i class="fa fa-pen icon-sm text-muted"></i>
                    <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                    <input type="hidden" name="avatar_remove" />
                </label>
                <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancel avatar">
                    <i class="ki ki-bold-close icon-xs text-muted"></i>
                </span>
            </div>
            <span class="form-text text-muted">{{ __('Allowed file types: png, jpg, jpeg.') }}</span>
            @error('avatar')
            @component('components.serverValidation')
            {{ $message }}
            @endcomponent
            @enderror
        </div>
    </div>
</div>
<!--end: Wizard Step 1-->