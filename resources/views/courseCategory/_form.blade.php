<div class="modal-body">
    <div class="card-body">
        <div class="form-group row">
            <div class="col-lg-12">
                {{ Form::label('name', 'Name <span style="color: red">*</span>','',false ) }}
                {{ Form::text('name',null, ['class' => 'form-control', 'placeholder' => 'Enter name']) }}
                @error('name')
                @component('components.serverValidation')
                {{ $message }}
                @endcomponent
                @enderror
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-12">
                {{ Form::label('status', 'Status') }}
                {{ Form::select('status', CommonHelper::getStatus(), null, ['class' => 'form-control']) }}
                @error('status')
                @component('components.serverValidation')
                {{ $message }}
                @endcomponent
                @enderror
            </div>
        </div>
        <input type="hidden" name="id" id="id">
    </div>
</div>
<div class="modal-footer">
    {{ Form::submit(__('Save'), ['class' => 'btn btn-primary mr-2', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'title' => __('Save')]) }}
    {{ Form::button(__('Reset'), ['type' => 'reset','class' => 'btn btn-danger', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'title' => __('Reset')]) }}
</div>