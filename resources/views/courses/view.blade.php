{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('courses-view-title', $course) }}
@endsection

{{-- Content --}}
@section('content')
<!--begin::Profile Card-->
<div class="">
    <div class="card card-custom card-stretch">
        <div class="card-header">
            @component('components.setActionBtn')
            @slot('title')
            View course details
            @endslot
            @slot('editRoute')
            {{ route('courses.edit', $course->id) }}
            @endslot
            @slot('deleteRoute')
            {{ route('courses.delete', $course->id) }}
            @endslot
            @slot('cancelRoute')
            {{ route('courses.index') }}
            @endslot
            @endcomponent
        </div>
        <!-- Course Details Section -->
        <div class="card-body py-4">
            <div class="d-flex align-items-center">
                <div class="symbol symbol-60 symbol-xxl-100 mr-5 align-self-start align-self-xxl-center">
                    <div class="symbol-label" style="background-image:url({{asset($course->cover_image_url)}});width: 80px;height: 80px;"></div>
                </div>
                <div>
                    <a href="javascript:void(0)" class="font-weight-bolder font-size-h5 text-dark-75 text-hover-primary">{{ $course->name ?? config('constants.DEFAULT_MSG') }}</a>
                    <div class="text-muted">{{ $course->courseCategory->name ?? config('constants.DEFAULT_MSG') }}</div>
                </div>
            </div>
            <div class="row mt-5 mb-5 view-detail">
                <div class="col-md-6 border-rght">
                    <h5 class="text-dark font-weight-bold mb-2">{{ __('Course Details') }}</h5>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Course category name:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $course->courseCategory->name ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Name:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $course->name ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Description:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $course->description ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Currency:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ $course->currency ?? config('constants.DEFAULT_MSG') }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Course Price:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{!! $course->price_with_currency_label ?? config('constants.DEFAULT_MSG') !!}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Course Special Price:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{!! $course->special_price_with_currency_label ?? config('constants.DEFAULT_MSG') !!}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Trending Course:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ ($course->trending) ? 'Yes' : 'No' }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Trainer Name:') }}</label>
                        <div class="col-8">
                            <span class="form-control-plaintext">{{ ($course->trainer) ? $course->trainer->full_name : config('constants.DEFAULT_MSG')  }}</span>
                        </div>
                    </div>
                    <div class="form-group row my-0">
                        <label class="col-4 col-form-label font-weight-bolder">{{ __('Status:') }}</label>
                        <div class="col-8 d-flex align-items-center">
                            @php
                            $checked = ($course->status) ? 'checked' : '';
                            @endphp
                            <input data-switch="true" id="status-change" type="checkbox" {{$checked}} data-on-text="{{ config('constants.ACTIVE_LABEL') }}" data-handle-width="60" data-off-text="{{ config('constants.INACTIVE_LABEL') }}" data-on-color="primary" />
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5 class="text-dark font-weight-bold mb-5">{{ __('Course Feature') }}</h5>
                    <div class="accordion accordion-light accordion-light-borderless accordion-svg-toggle" id="accordionExample1">
                        <!--begin::Item-->
                        <div class="card">
                            <!--begin::Header-->
                            <div class="card-header" id="headingOne1">
                                @forelse($course->courseFeatures as $key => $value)
                                <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOne1" aria-expanded="false" aria-controls="collapseOne1" role="button">
                                    <span class="svg-icon svg-icon-primary">
                                        <!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Angle-double-right.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                                <path d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z" fill="#000000" fill-rule="nonzero"></path>
                                                <path d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999)"></path>
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>
                                    <div class="card-label text-dark pl-4">{{ $value->title ?? config('constants.DEFAULT_MSG') }}</div>
                                </div>
                                @empty
                                <p>Course feature not found.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!--end::Body-->
    </div>
    <!-- Course Curriculums Sections -->
    <div class="card card-custom card-stretch mt-6 course-curr">
        <div class="card-body py-4">
            <h5 class="text-dark font-weight-bold mb-2">{{ __('Course Curriculums') }}</h5>

            @php $index=0; @endphp
            @forelse($course->courseCurriculumns as $key => $value)
            @php $index++; @endphp
            @if(isset($value->parent->title))
            <div class="view-box">
                <div class="form-group my-2">
                    <label class="col-form-label p-0 font-weight-bolder">{{ __('Section Name:') }}</label>
                    <div class="form-control-plaintext p-0">{{ $value->parent->title ?? config('constants.DEFAULT_MSG') }}
                    </div>
                </div>
                <div class="form-group my-2">
                    <label class="col-form-label p-0 font-weight-bolder">{{ __('Section Description:') }}</label>
                    <div class="form-control-plaintext p-0">{{ $value->parent->description ?? config('constants.DEFAULT_MSG') }}
                    </div>
                </div>
                <div class="accordion mt-4 accordion-light accordion-light-borderless accordion-svg-toggle" id="faq">
                    @foreach($value->children as $childrenKey => $children)
                    @php $index++; @endphp
                    <div class="card">
                        <div class="card-header" id="faqHeading{{$index}}">
                            <a class="card-title p-0 text-dark collapsed" data-toggle="collapse" href="#faq{{$index}}" aria-expanded="false" aria-controls="faq{{$index}}" role="button">
                                <span class="svg-icon svg-icon-primary">
                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Angle-double-right.svg-->
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                            <path d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z" fill="#000000" fill-rule="nonzero"></path>
                                            <path d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999)"></path>
                                        </g>
                                    </svg>
                                    <!--end::Svg Icon-->
                                </span>
                                <div class="card-label text-dark pl-4">{{ $children->title ?? config('constants.DEFAULT_MSG') }}</div>
                            </a>
                        </div>
                        <div id="faq{{$index}}" class="collapse" aria-labelledby="faqHeading{{$index}}" data-parent="#faq" style="">
                            <div class="card-body text-dark-50 font-size-lg pl-12">{{ $children->description ?? config('constants.DEFAULT_MSG') }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @empty
            <p>Course curriculums not found.</p>
            @endforelse
        </div>
    </div>
    <!-- FAQ Sections -->
    <div class="card card-custom card-stretch mt-6">
        <div class="card-body py-4">
            <h5 class="text-dark font-weight-bold mb-2">{{ __('Course FAQs') }}</h5>
            @forelse($course->courseFaqs as $key => $value)
            @php $index++; @endphp
            <div class="accordion accordion-light accordion-light-borderless accordion-svg-toggle" id="faq">
                <div class="card">
                    <div class="card-header" id="faqHeading{{$index}}">
                        <a class="card-title text-dark collapsed" data-toggle="collapse" href="#faq{{$index}}" aria-expanded="false" aria-controls="faq{{$index}}" role="button">
                            <span class="svg-icon svg-icon-primary">
                                <!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Angle-double-right.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                        <path d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z" fill="#000000" fill-rule="nonzero"></path>
                                        <path d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999)"></path>
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                            <div class="card-label text-dark pl-4">{{ $value->title ?? config('constants.DEFAULT_MSG') }}</div>
                        </a>
                    </div>
                    <div id="faq{{$index}}" class="collapse" aria-labelledby="faqHeading{{$index}}" data-parent="#faq" style="">
                        <div class="card-body text-dark-50 font-size-lg pl-12">{{ $value->description ?? config('constants.DEFAULT_MSG') }}</div>
                    </div>
                </div>
            </div>
            @empty
            <p>Course FAQs not found.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

{{-- Style Section --}}
@section('styles')
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script src="{{ asset('js/pages/crud/forms/widgets/bootstrap-switch.js') }}"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {
        // Update the status
        $('#status-change').on('switchChange.bootstrapSwitch', function(e, state) {
            $.ajax({
                type: 'POST',
                url: "{{ route('courses.update.status') }}",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    status: e.target.checked,
                    id: "{{$course->id}}"
                },
                success: function(data) {
                    toastr.success("Status has been updated.");
                },
                error: function(data) {
                    toastr.error("Status has not been updated.");
                }
            });
        });
    });
</script>
@endsection