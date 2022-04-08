@if($activityLogs->count() > 0)
@foreach($activityLogs as $key => $value)
<div id="block-{{ $value->id }}" class="d-flex align-items-center bg-light-success rounded p-5 mb-9" data-id="{{ $value->id }}">
    <!--begin::Icon-->
    <span id="spanIcon-{{ $value->id }}" class="svg-icon svg-icon-success mr-5">
        <span class="svg-icon mr-5">
            <span class="svg-icon svg-icon-primary svg-icon-2x">
                <!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Communication\Clipboard-list.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24" />
                        <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#000000" opacity="0.3" />
                        <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000" />
                        <rect fill="#000000" opacity="0.3" x="10" y="9" width="7" height="2" rx="1" />
                        <rect fill="#000000" opacity="0.3" x="7" y="9" width="2" height="2" rx="1" />
                        <rect fill="#000000" opacity="0.3" x="7" y="13" width="2" height="2" rx="1" />
                        <rect fill="#000000" opacity="0.3" x="10" y="13" width="7" height="2" rx="1" />
                        <rect fill="#000000" opacity="0.3" x="7" y="17" width="2" height="2" rx="1" />
                        <rect fill="#000000" opacity="0.3" x="10" y="17" width="7" height="2" rx="1" />
                    </g>
                </svg>
                <!--end::Svg Icon--></span>
        </span>
    </span>
    <!--end::Icon-->
    <!--begin::Title-->
    <div class="d-flex flex-column flex-grow-1 mr-2">
        <!-- <a href="void:javascript(0)" class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">{{ $value->message ?? '' }}</a> -->
        <span class="font-weight-bold text-dark-75 font-size-lg mb-1">{{ $value->message ?? '' }}</span>
    </div>
    <!--end::Title-->
    <!--begin::Lable-->
    <span class="font-weight-bolder text-success py-1 font-size-lg">{{ $value->created_at ?? '' }}</span>
    <!--end::Lable-->
</div>
@endforeach
@else
<div class="d-flex align-items-center bg-light-success rounded p-5 mb-9">
    <!--begin::Icon-->
    <span class="svg-icon svg-icon-success  mr-5">
        <span class="svg-icon mr-5">
            <span class="svg-icon svg-icon-primary svg-icon-2x">
                <!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Code\Compiling.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24" />
                        <path d="M2.56066017,10.6819805 L4.68198052,8.56066017 C5.26776695,7.97487373 6.21751442,7.97487373 6.80330086,8.56066017 L8.9246212,10.6819805 C9.51040764,11.267767 9.51040764,12.2175144 8.9246212,12.8033009 L6.80330086,14.9246212 C6.21751442,15.5104076 5.26776695,15.5104076 4.68198052,14.9246212 L2.56066017,12.8033009 C1.97487373,12.2175144 1.97487373,11.267767 2.56066017,10.6819805 Z M14.5606602,10.6819805 L16.6819805,8.56066017 C17.267767,7.97487373 18.2175144,7.97487373 18.8033009,8.56066017 L20.9246212,10.6819805 C21.5104076,11.267767 21.5104076,12.2175144 20.9246212,12.8033009 L18.8033009,14.9246212 C18.2175144,15.5104076 17.267767,15.5104076 16.6819805,14.9246212 L14.5606602,12.8033009 C13.9748737,12.2175144 13.9748737,11.267767 14.5606602,10.6819805 Z" fill="#000000" opacity="0.3" />
                        <path d="M8.56066017,16.6819805 L10.6819805,14.5606602 C11.267767,13.9748737 12.2175144,13.9748737 12.8033009,14.5606602 L14.9246212,16.6819805 C15.5104076,17.267767 15.5104076,18.2175144 14.9246212,18.8033009 L12.8033009,20.9246212 C12.2175144,21.5104076 11.267767,21.5104076 10.6819805,20.9246212 L8.56066017,18.8033009 C7.97487373,18.2175144 7.97487373,17.267767 8.56066017,16.6819805 Z M8.56066017,4.68198052 L10.6819805,2.56066017 C11.267767,1.97487373 12.2175144,1.97487373 12.8033009,2.56066017 L14.9246212,4.68198052 C15.5104076,5.26776695 15.5104076,6.21751442 14.9246212,6.80330086 L12.8033009,8.9246212 C12.2175144,9.51040764 11.267767,9.51040764 10.6819805,8.9246212 L8.56066017,6.80330086 C7.97487373,6.21751442 7.97487373,5.26776695 8.56066017,4.68198052 Z" fill="#000000" />
                    </g>
                </svg>
                <!--end::Svg Icon--></span>
        </span>
    </span>
    <!--end::Icon-->
    <!--begin::Title-->
    <div class="d-flex flex-column flex-grow-1 mr-2">
        <!-- <a href="#" class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">{{ $value->title ?? '' }}</a> -->
        <span class="text-muted font-weight-bold">{{ __('Activity log not found.') }}</span>
    </div>
    <!--end::Title-->
    <!--begin::Lable-->
    <!-- <span class="font-weight-bolder text-success py-1 font-size-lg">{{ $value->created_at ?? '' }}</span> -->
    <!--end::Lable-->
</div>
@endif