<div id="content" class="tab-content" role="tablist">

    <!--begin: Wizard Step 1-->
    @include('courses.details')
    <!--end: Wizard Step 1-->

    <!--begin: Wizard Step 2-->
    @include('courses.key-features')
    <!--end: Wizard Step 2-->

    <!--begin: Wizard Step 3-->
    @include('courses.curriculum')
    <!--end: Wizard Step 3-->

    <!--begin: Wizard Step 4-->
    @include('courses.faq')
    <!--end: Wizard Step 4-->
</div>

{{-- Style Section --}}
@section('styles')
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script>
    // Curriculum section route name
    const sectionListing = @json(route('courses.section.listing'));
    const sidebarParentSectionListing = @json(route('courses.parent.section.listing'));
    const deleteSectionUrl = @json(route('courses.delete.section'));

    // Curriculum topic route name
    const topicListing = @json(route('courses.topic.listing'));
    const deleteTopicUrl = @json(route('courses.delete.topic'));

    // FAQ topic route name
    const getFaqListing = @json(route('courses.faq.listing'));
    const getFaq = @json(route('courses.getfaq'));
    const faqDelete = @json(route('courses.delete.faq'));
</script>
<script src="{{ asset('js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/custome-validation/additional-methods.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/custome-validation/course-detail.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/crud/course-section.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/crud/course-topic.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/pages/crud/course-faq.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    jQuery(document).ready(function() {
        $('#addNewSection').css('display', 'none');
        $('.addNewFaq').css('display', 'none');
        var courseId = $("#course-id").val();

        /**
         * For Edit page set the init data
         */
        if (courseId != '' && courseId != undefined) {
            getParentSectionListing(courseId);
            getFqaListing(courseId);
        }

        /**
         * Manage the next and previous button
         */
        $(document).on('click', '.btnNext, .btnPrevious', function(e) {
            $("#" + $(this).data('id')).trigger('click');
        });

        /**
         * Set the course detail form validation
         * If for is not valid so not redirect to any tab
         */
        $(document).on('click', '.nav-link', function(event) {
            event.preventDefault();
            if (!$("#course-details").valid()) {
                location.reload(true);
                toastr.error("Please fill the course detail form");
            }
        });
    });

    /**
     * Read the cover image URL and set the cover image
     */
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#view_cover_image').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    /**
     * View the cover image
     */
    $(document).on('change', '#cover_image', function() {
        readURL(this);
    });

    /** 
     * Apply select2
     */
    $(".select2").select2({
        width: '100%'
    });

    /** 
     * Reset the select2 value 
     */
    $(document).on("click", "#reset", function() {
        $('.select2').val('').trigger('change');
        $(".image-input-wrapper").css({
            "background-image": ""
        });
        $("#avatar").val('');
    });

    /**
     * Calculate the discount on price
     */
    $(document).on('keyup', '#course_price,#course_special_price', function() {
        var course_price = parseFloat($('#course_price').val());
        var course_special_price = parseFloat($('#course_special_price').val());

        if (course_price && course_special_price && course_price > course_special_price) {
            var percentage = (((course_price - course_special_price) * 100) / course_price);
            $(".dis-text").remove();
            $('<span class="dis-text">' + percentage.toFixed(2) + '% discount added to this course. </span>').insertAfter("#course_special_price");
        } else {
            $(".dis-text").remove();
        }
    });

    /**
     * Save the course detail on database
     */
    $(document).on("click", ".saveDetails", function(e, state) {
        if ($("#course-details").valid()) {
            var formData = new FormData($('#course-details')[0]);
            if ($("#course-id").val() != undefined) {
                formData.append("id", $("#course-id").val());
            }
            e.preventDefault();
            saveCourseDetail(formData);
        }
    });


    /**
     * Save the multiple key features
     */
    $(document).on("click", ".saveKeyFeatures", function(e, state) {
        if ($("#course-key-features").valid()) {
            var formData = new FormData($('#course-key-features')[0]);
            if ($("#course-id").val() != undefined) {
                formData.append("id", $("#course-id").val());
            }
            e.preventDefault();
            saveCourseKeyFeatures(formData);
        }
    });


    /**
     * Ajax call for save the course details
     * 
     */
    function saveCourseDetail(formData) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            url: $("#course-details").attr("action"),
            type: $("#course-details").attr("method"),
            dataType: "JSON",
            data: formData,
            processData: false,
            contentType: false,
            async: true,
            cache: false,
            success: function(respose) {
                if (respose.success === true) {
                    $("#course-id").val(respose.data.id);
                    toastr.success(respose.message);
                    $('#tab-B').trigger("click");
                } else {
                    toastr.error(respose.message);
                }
            },
            error: function(respose) {
                let data = respose.responseJSON;
                if (data.status === 400) {
                    var errors = data.errors;
                    var errorsHtml = '<div class="alert alert-danger"><ul>';
                    errorsHtml += '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
                    $.each(errors, function(key, value) {
                        errorsHtml += '<li>' + value[0] + '</li>';
                    });
                    errorsHtml += '</ul></div>';
                    $('.errors').html(errorsHtml);
                }
            },
        });
        return false;
    }

    /**
     * Ajax call for save the key features
     */
    function saveCourseKeyFeatures(formData) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            url: $("#course-key-features").attr("action"),
            type: $("#course-key-features").attr("method"),
            dataType: "JSON",
            data: formData,
            processData: false,
            contentType: false,
            async: true,
            cache: false,
            success: function(respose) {
                if (respose.success === true) {
                    toastr.success(respose.message);
                    $('#tab-C').trigger("click");
                } else {
                    toastr.error(respose.message);
                }
            },
            error: function(respose) {
                let data = respose.responseJSON;
                if (data.status === 400) {
                    var errors = data.errors;
                    var errorsHtml = '<div class="alert alert-danger"><ul>';
                    errorsHtml += '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
                    $.each(errors, function(key, value) {
                        errorsHtml += '<li>' + value[0] + '</li>';
                    });
                    errorsHtml += '</ul></div>';
                    $('.errors').html(errorsHtml);
                }
            },
        });
        return false;
    }

    /**
     * Add more course feature
     */
    var count = $('.kyc-frm .form-group.row').length;
    $(document).on('click', '.add-keyword', function() {
        var html = `<div class="form-group row align-items-center">
                        <div class="col-11">
                            {{ Form::text('course_features[${count}][title]',null, ['class' => 'form-control', 'placeholder' => 'Add here']) }}
                        </div>                    
                        <div class="col-1 p-0">
                            <i class="la la-trash del-ic remove-keyword" data-toggle="tooltip" data-theme="dark" title="{{ __('Delete') }}"></i>                            
                        </div>
                    </div>`;
        $('.add-new-key-features').append(html);
        count++;
    });

    /**
     * Remove course feature
     */
    $(document).on('click', '.remove-keyword', function() {
        $(this).closest('.form-group.row').remove();
    });

    /**
     * Click on Add new topic button
     * Show the topic into database
     */
    $(document).on("click", "#addNewTopic", function(e, state) {
        $(this).hide();
        var topicForm = '<div class="white-box">{{ Form::open(["route" => "courses.curriculum" ,"method" => "POST" , "name" => "course-topic", "id" => "course-topic", "enctype" => "multipart/form-data"]) }}\n\
            \n\<div class="row">\n\
            \n\<div class="form-group col-xl-12">\n\
            \n\{{ Form::label("topic_name", __("Topic Name")) }}\n\
            \n\{{ Form::text("title",null, ["class" => "form-control topicTitleTextbox", "placeholder" => "Enter topic name"]) }}\n\
            \n\</div>\n\
            \n\<div class="form-group col-xl-12">\n\
            \n\{{ Form::label("topic_description", __("Topic Description")) }}\n\
            \n\ {{ Form::textarea("description",null, ["class" => "form-control topicDescriptionTextbox","rows" => 5, "style" => "resize: none;", "placeholder" => "Enter topic description"]) }}\n\
            \n\</div>\n\
            \n\</div>\n\
            \n\<div class="mt-2 d-flex justify-content-end">\n\
                \n\{{ Form::button("Save", ["class" => "btn btn-primary saveTopic", "data-toggle" => "tooltip", "data-theme" => "dark", "title" => "Save"]) }}\n\
                \n\{{ Form::button("Cancel", ["type" => "reset","id" => "reset","class" => "btn btn-danger ml-2", "data-toggle" => "tooltip", "data-theme" => "dark", "title" => "Cancel"]) }}\n\
                \n\</div>\n\
                \n\<input type="hidden" name"parent_id" id="parent_id" value="' + $(this).data('id') + '">\n\
                \n\<input type="hidden" name="topicId" id="topicId">{{ Form::close() }}\n\
            \n\</div>';
        $(".showTopicForm").show();
        $(".showTopicForm").html(topicForm);
        $(".showTopicListing").hide();
    });
</script>
@endsection