@include('customers.personal-info')

@include('customers.contact-info')

@if(!isset($user))
@include('customers.create-password')
@endif
<div class="d-flex justify-content-between border-top pt-10">
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
<link href=" {{ asset('css/pages/wizard/wizard-3.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/intlTelInput.css') }}" rel="stylesheet" type="text/css" />
<style>
    .iti {display: inherit;}
    .iti__flag {background-image: url("{{ asset('media/flags.png') }}");}

    @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
    .iti__flag {background-image: url("{{ asset('media/flags@2x.png') }}");}
    }
</style>
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script src="{{ asset('js/pages/customer-wizard.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/intlTelInput.js') }}" type="text/javascript"></script>
<script>
    var avatar = new KTImageInput('kt_profile_avatar');
    avatar.on('remove', function(imageInput) {
        $('input[type=hidden][name=avatar_remove]').val('1');
    });
    /** Apply select2 */
    $(".select2").select2({
        width: '100%'
    });

    if($('#phone_number').length) {
        createPhoneNumberInput('#phone_number'); // Pass id of the input
    }

    $('#user_type').on('change', function() {
        var val = $(this).val();
        if (val == 2) {
            $('div#company-details').show();
            return;
        }
        $('div#company-details').hide();
    });
</script>
@endsection