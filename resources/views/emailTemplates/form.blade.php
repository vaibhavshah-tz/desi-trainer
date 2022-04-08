<div class="card-body">
    <div class="form-group row">
        {{ Form::hidden('template-id', $emailTemplate->id ?? 0, ['id' => 'template-id']) }}
        <div class="col-lg-6">
            {{ Form::label('name', __('Name')) }}
            {{ Form::text('name',null, ['class' => 'form-control '.($errors->has('name') ? 'is-invalid' : ''), 'placeholder' => 'Enter name', 'id' => 'template-name']) }}
            @error('name')
                <span class="invalid-feedback" role="alert">
                    {{ $message }}
                </span>
            @enderror
        </div>
        <div class="col-lg-6">
            {{ Form::label('subject', __('Subject')) }}
            {{ Form::text('subject',null, ['class' => 'form-control '.($errors->has('subject') ? 'is-invalid' : ''), 'placeholder' => 'Enter subject']) }}
            @error('subject')
                <span class="invalid-feedback" role="alert">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    <div class="form-group row">
        <div class="col-lg-12">
            {{ Form::label('body', __('Body')) }}
            {{ Form::textarea('body',null, ['class' => 'form-control summernote '.($errors->has('body') ? 'is-invalid' : ''), 'placeholder' => 'Enter body']) }}
            @error('body')
                <span class="invalid-feedback" role="alert">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
    <h5 class="text-dark font-weight-bold mb-10">{{ __('Keywords:') }}</h5>
    <div class="keywords-container">
        @php $keywords = !empty(old('keywords')) ? old('keywords') : $emailTemplate->keywords ?? [] @endphp
        @forelse($keywords as $key => $keyword)
            <div class="form-group row">
                <div class="col-lg-5">
                    {{ Form::text("keywords[$key][key]",$keyword['key'] ?? '', ['class' => 'form-control', 'placeholder' => 'Enter key']) }}
                </div>
                <div class="col-lg-5">
                    {{ Form::text("keywords[$key][description]",$keyword['description'] ?? '', ['class' => 'form-control', 'placeholder' => 'Enter description']) }}
                </div>
                @if($loop->first)
                    <div class="col-lg-2">
                        <a href="javascript:;" class="btn btn-sm font-weight-bolder btn-light-primary add-keyword">
                        <i class="la la-plus"></i>{{ __('Add') }}</a>
                    </div>
                @else
                    <div class="col-md-2">
                        <a href="javascript:;" class="btn btn-sm font-weight-bolder btn-light-danger remove-keyword">
                        <i class="la la-trash-o"></i>{{ __('Delete') }}</a>
                    </div>
                @endif
            </div>
        @empty
            <div class="form-group row">
                <div class="col-lg-5">
                    {{ Form::text('keywords[0][key]',null, ['class' => 'form-control', 'placeholder' => 'Enter key']) }}
                </div>
                <div class="col-lg-5">
                    {{ Form::text('keywords[0][description]',null, ['class' => 'form-control', 'placeholder' => 'Enter description']) }}
                </div>
                <div class="col-lg-2">
                    <a href="javascript:;" class="btn btn-sm font-weight-bolder btn-light-primary add-keyword">
                    <i class="la la-plus"></i>{{ __('Add') }}</a>
                </div>
            </div>
        @endforelse
    </div>
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-lg-12 text-right">
            {{ Form::submit(__('Save'), ['class' => 'btn btn-primary mr-2']) }}
            {{ Form::button(__('Reset'), ['type' => 'reset','class' => 'btn btn-danger']) }}
        </div>
    </div>
</div>
{{-- Scripts Section --}}
@section('scripts')
<script src="{{ asset('js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/custome-validation/email-template.js') }}" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $('.summernote').summernote({
            height: 200
        });

        var count = $('.keywords-container .form-group.row').length;
        $('.add-keyword').on('click', function() {
            var html = `<div class="form-group row">
                    <div class="col-lg-5">
                        {{ Form::text('keywords[${count}][key]',null, ['class' => 'form-control', 'placeholder' => 'Enter key']) }}
                    </div>
                    <div class="col-lg-5">
                        {{ Form::text('keywords[${count}][description]',null, ['class' => 'form-control', 'placeholder' => 'Enter description']) }}
                    </div>
                    <div class="col-md-2">
                        <a href="javascript:;" class="btn btn-sm font-weight-bolder btn-light-danger remove-keyword">
                        <i class="la la-trash-o"></i>{{ __('Delete') }}</a>
                    </div>
                </div>`;
                $('.keywords-container').append(html);
                count++;
        });

        $(document).on('click', '.remove-keyword', function() {
            $(this).closest('.form-group.row').remove();
        });
    });
</script>
@endsection