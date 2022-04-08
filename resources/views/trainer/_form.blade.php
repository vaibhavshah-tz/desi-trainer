<!--begin: Wizard Step 1-->
@include('trainer.personalInfo')
<!--end: Wizard Step 1-->
<!--begin: Wizard Step 2-->
@include('trainer.contactInfo')
<!--end: Wizard Step 2-->
<!--begin: Wizard Step 3-->
@include('trainer.skill')
<!--end: Wizard Step 3-->
<!--begin: Wizard Step 4-->
@include('trainer.price-charge')
<!--end: Wizard Step 4-->

@if(!isset($trainer))
@include('trainer.create-password')
@endif
<!--begin: Wizard Actions-->
<div class="d-flex justify-content-between border-top mt-5 pt-10">
    <div class="mr-2">
        <button type="button" class="btn btn-light-primary" data-wizard-type="action-prev" data-toggle="tooltip" data-theme="dark" title="{{ __('Previous') }}">{{ __('Previous') }}</button>
    </div>
    <div>
        <button type="button" class="btn btn-success" data-wizard-type="action-submit" data-toggle="tooltip" data-theme="dark" title="{{ __('Save') }}">{{ __('Save') }}</button>
        <button type="button" class="btn btn-primary" data-wizard-type="action-next" data-toggle="tooltip" data-theme="dark" title="{{ __('Next') }}">{{ __('Next') }}</button>
    </div>
</div>

{{-- Style Section --}}
@section('styles')
<link href="{{ asset('css/pages/wizard/wizard-3.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/intlTelInput.css') }}" rel="stylesheet" type="text/css" />
<style>
    .iti {
        display: inherit;
    }

    .iti__flag {
        background-image: url("{{ asset('media/flags.png') }}");
    }

    @media (-webkit-min-device-pixel-ratio: 2),
    (min-resolution: 192dpi) {
        .iti__flag {
            background-image: url("{{ asset('media/flags@2x.png') }}");
        }
    }
</style>
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script src="{{ asset('js/pages/custome-validation/trainer-wizard.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/intlTelInput.js') }}" type="text/javascript"></script>
<script>
    var trainerId = <?php echo !empty($trainer) ? json_encode($trainer) : '0' ?>;
</script>
<script>
    var avatar = new KTImageInput('kt_profile_avatar');
    avatar.on('remove', function(imageInput) {
        $('input[type=hidden][name=avatar_remove]').val('1');
    });

    if (trainerId.id != undefined) {
        $(".commentBox").hide();
        $(".addNewComment").show();
        $(".commentList").show();
    } else {
        $(".commentBox").show();
        $(".addNewComment").hide();
        $(".commentList").hide();
    }


    if ($('#phone_number').length) {
        createPhoneNumberInput('#phone_number'); // Pass id of the input
    }

    /** Apply select2 */
    $(".select2").select2({
        width: '100%',
    });
    $(".primary_skill").select2({
        placeholder: "Select primary skill",
        allowClear: true,
        width: '100%'
    });
    $(".ticket_type").select2({
        placeholder: "Select Interested in",
        allowClear: true,
        width: '100%'
    });
    // Hide the image button
    $(document).on('change', '#resume', function() {
        $("#view_resume").hide();
    });
    // $("select").on("select2:close", function(e) {
    //     $(this).valid();
    // });

    // Hide the note button
    $(document).on('click', '.addNewComment', function() {
        $(".commentBox").toggle();
    });

    /**
     * Set the primary skill based on course category value
     */
    $(document).on("change", "#course_category_id", function(e) {
        $.ajax({
            type: 'GET',
            url: "{{ route('categorywise.skills') }}",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: {
                course_category_id: $(this).val(),
            },
            success: function(data) {
                var $newOption = '';
                $("#primary_skill").empty();
                // $newOption += '<option value="">Select primary skills</option>';
                if (data.length > 0) {
                    if (data.length > 0) {
                        $.each(data, function(index, value) {
                            $newOption += '<option value="' + value.id + '">' + value.name + '</option>';
                        });
                    }
                }
                $("#primary_skill").append($newOption).trigger('change');
            },
            error: function(data) {
                toastr.error("Primary skill has not been not set.");
            }
        });
    });
</script>
@endsection