{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('notification') }}
@endsection

{{-- Content --}}
@section('content')

{{-- Dashboard 1 --}}
<!--begin::Card-->
<div class="card card-custom">
    <div class="card-header">
        <div class="card-title">
            <span class="card-icon">
                <i class="flaticon2-bell-4  text-primary"></i>
            </span>
            <h3 class="card-label">{{ __('Notification Listing') }}</h3>
        </div>
    </div>
    <div class="card-body pt-0" id="post-data">
        <!--begin::Item-->
        <h3 class="card-title font-weight-bolder text-dark"></h3>

        @include('notifications._data')
    </div>
</div>
<div class="example-preview" id="kt_blockui_content"></div>
<!--end::Card-->

@endsection
@section('styles')
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script type="text/javascript">
    var page = 1;
    var totalPages = "{{ $notifications->lastPage() }}";
    $(window).scroll(function() {
        if ($(window).scrollTop() + $(window).height() >= $(document).height()) {
            page++;
            if (page <= totalPages) {
                loadMoreData(page);
            } else {
                setLoader("No more records found");
                return;
            }
        }
    });

    function loadMoreData(page) {
        $.ajax({
                url: '?page=' + page,
                type: "get",
                beforeSend: function() {
                    setLoader("Loading more notification...");
                }
            })
            .done(function(data) {
                if (data.html == " ") {
                    setLoader("No more records found");
                    return;
                }
                $("#post-data").append(data.html);
            })
            .fail(function(jqXHR, ajaxOptions, thrownError) {
                alert('server not responding...');
            });
    }

    function setLoader(message) {
        KTApp.block('#kt_blockui_content', {
            overlayColor: '#000000',
            state: 'primary',
            message: message
        });

        setTimeout(function() {
            KTApp.unblock('#kt_blockui_content');
        }, 2000);
    }
</script>
@endsection