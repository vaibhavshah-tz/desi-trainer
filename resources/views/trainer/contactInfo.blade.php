<!--begin: Wizard Step 2-->
<div class="pb-5" data-wizard-type="step-content">
	<h4 class="mb-10 font-weight-bold text-dark">{{ __('Setup Your Contact Info') }}</h4>
	<div class="form-group row">
		<!-- <div class="col-lg-2">
			{{ Form::label('country_code', 'Country code <span style="color: red">*</span>','',false ) }}
			{{ Form::select('country_code', App\Models\Country::getAllCountryCode(), null, ['class' => 'form-control select2 '.($errors->has('country_code') ? 'is-invalid' : ''), 'placeholder' => 'Select country code', 'id' => 'country_code']) }}
			@error('country_code')
			@component('components.serverValidation')
			{{ $message }}
			@endcomponent
			@enderror
		</div> -->
		
		<div class="col-lg-6">
			{{ Form::label('country_id', 'Country <span style="color: red">*</span>','',false ) }}
			{{ Form::select('country_id', array_column(App\Models\Country::getAllCountry(), 'name','id'), null, ['class' => 'form-control select2', 'placeholder' => 'Select country', 'id' => 'country']) }}
			@error('country_id')
			@component('components.serverValidation')
			{{ $message }}
			@endcomponent
			@enderror
		</div>
		<div class="col-lg-6">
			{{ Form::label('zipcode', 'Zipcode <span style="color: red">*</span>','',false ) }}
			{{ Form::text('zipcode',null, ['class' => 'form-control', 'placeholder' => 'Enter zipcode']) }}
			@error('zipcode')
			@component('components.serverValidation')
			{{ $message }}
			@endcomponent
			@enderror
		</div>
	</div>
	<div class="form-group row">
		<div class="col-lg-6">
			{{ Form::label('city', 'City <span style="color: red">*</span>','',false ) }}
			{{ Form::text('city',null, ['class' => 'form-control', 'placeholder' => 'Enter city']) }}
			@error('city')
			@component('components.serverValidation')
			{{ $message }}
			@endcomponent
			@enderror
		</div>
		<div class="col-lg-6">
			{{ Form::label('timezone_id', 'Timezone <span style="color: red">*</span>','',false ) }}
			{{ Form::select('timezone_id', array_column(App\Models\Timezone::getAllTimezone(), 'label','id'), null, ['class' => 'form-control select2', 'placeholder' => 'Select timezone', 'id' => 'timezone_id']) }}
			@error('timezone_id')
			@component('components.serverValidation')
			{{ $message }}
			@endcomponent
			@enderror
		</div>
	</div>
	<div class="form-group row">
		@if(!isset($trainer) || Auth::user()->role_id == config('constants.ADMIN_ROLE.SUPER_ADMIN'))
		{{ Form::hidden('country_code', null, ['id' => 'country_code']) }}
		<div class="col-lg-6">
			{{ Form::label('phone_number', 'Phone number <span style="color: red">*</span>','',false ) }}
			{{ Form::text('phone_number',$trainer->full_phone_number ?? null, ['class' => 'form-control', 'placeholder' => 'Enter phone number', 'id' => 'phone_number']) }}
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
		@endif
		
	</div>
</div>
<!--end: Wizard Step 2-->