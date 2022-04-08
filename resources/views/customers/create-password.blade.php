<div class="pb-5" data-wizard-type="step-content">
    <h4 class="mb-10 font-weight-bold text-dark">Create password</h4>
    <div class="form-group row">
        <div class="col-lg-6">
            {{ Form::label('password', 'Password <span style="color: red">*</span>','',false) }}
            {{ Form::password('password', ['class' => 'form-control '.($errors->has('password') ? 'is-invalid' : ''), 'placeholder' => 'Enter password', 'autocomplete' => 'new-password']) }}
            @error('password')
                @component('components.serverValidation')
                    {{ $message }}
                @endcomponent
            @enderror
        </div>
        <div class="col-lg-6">
            {{ Form::label('password_confirmation', 'Confirm Password <span style="color: red">*</span>','',false) }}
            {{ Form::password('password_confirmation', ['class' => 'form-control '.($errors->has('password_confirmation') ? 'is-invalid' : ''), 'placeholder' => 'Enter confirm password', 'autocomplete' => 'new-password']) }}
            @error('password_confirmation')
                @component('components.serverValidation')
                    {{ $message }}
                @endcomponent
            @enderror
        </div>
    </div>
</div>