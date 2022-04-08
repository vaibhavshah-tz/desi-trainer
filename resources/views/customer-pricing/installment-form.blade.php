@csrf
<div class="modal-body">
    <div class="card-body">
        <div class="form-group row">
            {{ Form::hidden('installment_id', $installment->id ?? 0, ['id' => 'installment_id']) }}
            <div class="col-lg-12">
                {{ Form::label('name', 'Name <span style="color: red">*</span>','',false ) }}
                {{ Form::text('name', $installment->name ?? null, ['class' => 'form-control', 'placeholder' => 'Enter name e.g. First Installment']) }}
                @error('name')
                    @component('components.serverValidation')
                        {{ $message }}
                    @endcomponent
                @enderror
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-12">
                {{ Form::label('amount', 'Amount <span style="color: red">*</span>','',false ) }}
                {{ Form::number('amount', $installment->amount ?? null, ['min' => '0','class' => 'form-control '.($errors->has('quote') ? 'is-invalid' : ''), 'placeholder' => 'Enter amount', 'id' => 'amount']) }}
                @error('amount')
                    @component('components.serverValidation')
                        {{ $message }}
                    @endcomponent
                @enderror
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-12">
                {{ Form::label('due_date', 'Due Date <span style="color: red">*</span>','',false ) }}
                {{ Form::text('due_date', $installment->due_date ?? null, ['class' => 'form-control', 'placeholder' => 'Select date', 'readonly' => "readonly", 'id' => 'date']) }}
                @error('due_date')
                    @component('components.serverValidation')
                        {{ $message }}
                    @endcomponent
                @enderror
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    {{ Form::submit(__('Save'), ['class' => 'btn btn-primary mr-2', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'title' => __('Save')]) }}
    {{ Form::button(__('Reset'), ['type' => 'reset', 'id' => 'reset','class' => 'btn btn-danger', 'data-toggle' => 'tooltip', 'data-theme' => 'dark', 'title' => __('Reset')]) }}
</div>