<h3 class="card-title">{{ @trans($title) }}</h3>
<div class="card-toolbar">
    @if(@isset($editRoute))
    <a href="{{ $editRoute }}" class="btn btn-light-primary mr-2 btn-sm" data-toggle="tooltip" data-theme="dark" title="{{ @trans('Edit details') }}"><i class="flaticon2-contract"></i>{{ @trans('Edit') }}</a>
    @endif
    @if(@isset($viewRoute))
    <a href="{{ $viewRoute }}" class="btn btn-light-primary mr-2 btn-sm" data-toggle="tooltip" data-theme="dark" title="{{ @trans('View details') }}"><i class="flaticon-eye"></i>{{ @trans('View') }}</a>
    @endif
    @if(@isset($deleteRoute))
    <a href="{{ $deleteRoute }}" class="btn btn-light-danger mr-2 btn-sm delete-record" data-toggle="tooltip" data-theme="dark" title="{{ @trans('Delete') }}"><i class="flaticon-delete"></i>{{ @trans('Delete') }}</a>
    @endif
    @if(@isset($cancelRoute))
    <a href="{{ $cancelRoute }}" class="btn btn-light-success mr-2 btn-sm" data-toggle="tooltip" data-theme="dark" title="{{ @trans('Back') }}"><i class="flaticon-reply"></i>{{ @trans('Back') }}</a>
    @endif
</div>
@push('scripts')
<script>
    $(document).on('click', '.delete-record', function(e) {
        e.preventDefault();
        $me = $(this);
        Swal.fire({
            title: 'Are you sure?',
            text: "It will permanently deleted !",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "Yes, delete it!",
        }).then(function(data) {
            if (data.isConfirmed) {
                window.location.href = $me.attr('href');
            }
        });
    });
</script>
@endpush