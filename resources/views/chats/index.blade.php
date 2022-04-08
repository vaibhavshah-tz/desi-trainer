{{-- Extends layout --}}
@extends('layout.default')

{{-- Breadcrumb --}}
@section('breadcrumbs')
{{ Breadcrumbs::render('chats', request()->route('id')) }}
@endsection

{{-- Content --}}
@section('content')

<!--begin::Card-->
<ul class="nav nav-pills mb-3 justify-content-end chat-tab" id="pills-tab" role="tablist">
    <li class="nav-item">
        <a data-href="{{ route('chat.index',['id' => $ticketId]) }}" class="nav-link active getActiveChat" data-name="{{ config('constants.SENDER_TYPE.CUSTOMER') }}" id="pills-customer-tab" data-toggle="pill" href="#pills-customer" role="tab" aria-controls="pills-customer" aria-selected="true" data-toggle="tooltip" data-theme="dark" title="{{ __('Customer') }}">{{ __('Customer') }}</a>
    </li>
    <li class="nav-item">
        <a data-href="{{ route('chat.index',['id' => $ticketId, 'trainer_id' => $ticketDetails->trainer_id]) }}" class="nav-link getActiveChat {{ ($ticketDetails->trainer_id) ? '' : 'disabled' }}" id="pills-atrainer-tab" data-name="{{ config('constants.SENDER_TYPE.TRAINER') }}" data-toggle="pill" href="#pills-atrainer" role="tab" aria-controls="pills-atrainer" aria-selected="false" data-toggle="tooltip" data-theme="dark" title="{{ __('Assigned Trainer') }}">{{ __('Assigned Trainer') }}</a>
    </li>
    <li class="nav-item">
        @php $interestedTraierId = ''; @endphp
        @if($interestedTrainer->interestedTrainers->first())
        @php $interestedTraierId = $interestedTrainer->interestedTrainers->first()->id; @endphp
        @endif
        <a data-href="{{ route('chat.index',['id' => $ticketId, 'trainer_id' => $interestedTraierId]) }}" class="nav-link getActiveChat {{ ($interestedTraierId) ? '' : 'disabled' }}" id="pills-itrainer-tab" data-name="{{ config('constants.SENDER_TYPE.TRAINER') }}" data-toggle="pill" href="#pills-itrainer" role="tab" aria-controls="pills-itrainer" aria-selected="false" data-toggle="tooltip" data-theme="dark" title="{{ __('Interested Trainer') }}">{{ __('Interested Trainer') }}</a>
    </li>
</ul>
<div class="card card-custom chat-main-section">
    <!--begin::Example-->
    <div class="chat-main">
        <div class="tab-content" id="pills-tabContent">
            @php $chatData = []; $activeDiv = ''; @endphp
            @php
            $fullName = !empty($ticketDetails->customer->full_name) ? $ticketDetails->customer->full_name : '';
            $avatar = !empty($ticketDetails->customer->avatar_url) ? $ticketDetails->customer->avatar_url : '';
            $customerId = !empty($ticketDetails->customer->id) ? $ticketDetails->customer->id : '';
            $customerChannel = config('constants.CHANNEL_NAME.ADMIN_CUSTOMER') . $ticketDetails->ticket_id;
            @endphp
            @include('chats.customer')
            @php
            $fullName = !empty($ticketDetails->trainer->full_name) ? $ticketDetails->trainer->full_name : '';
            $avatar = !empty($ticketDetails->trainer->avatar_url) ? $ticketDetails->trainer->avatar_url : '';
            $trainerId = !empty($ticketDetails->trainer->id) ? $ticketDetails->trainer->id : '';
            $trainerChannel = config('constants.CHANNEL_NAME.ADMIN_TRAINER') . $ticketDetails->ticket_id .'-'. $trainerId;
            @endphp
            @include('chats.trainer')
            @include('chats.interestedTrainer')
            <input type="hidden" name="roomId" value="" id="roomId">
            <input type="hidden" name="customerChannel" id="customerChannel" value="{{ ($customerChannel) ? $customerChannel : config('constants.CHANNEL_NAME.ADMIN_CUSTOMER') . $ticketDetails->ticket_id }}">
            <input type="hidden" name="trainerChannel" id="trainerChannel" value="{{ ($trainerChannel) ? $trainerChannel : config('constants.CHANNEL_NAME.ADMIN_TRAINER') . $ticketDetails->ticket_id . '-' . $trainerId }}">
            <input type="hidden" name="receiverType" id="receiverType" value="{{ config('constants.SENDER_TYPE.CUSTOMER') }}">
        </div>
    </div>
</div>
<!--end::Card-->
@endsection

@section('styles')

@endsection

{{-- Scripts Section --}}
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.3.1/socket.io.js"></script>

<script>
    var url = "{{ env('APP_URL') }}";
    var ticketId = "{{ $ticketDetails->ticket_id }}";
    var customerId = "{{ $customerId }}";
    var trainerId = '';
    var socket = io.connect(url + ':8890');
    var roomID = $("#customerChannel").val();
    var media = '';
    var activeChat = "{{ \Request::route('trainer_id') }}";
    var activeTabUrl = window.location.href;
    var activeTab = activeTabUrl.substring(activeTabUrl.indexOf("#") + 1);

    if (activeChat != '') {
        $(".tab-pane").removeClass("active in");
        $("#" + activeTab).addClass("active in");
        $('a[href="#' + activeTab + '"]').tab('show');

        customerId = '';
        trainerId = "{{ $trainerId }}";
        roomID = $("#trainerChannel").val();
        $("#receiverType").val("{{ config('constants.SENDER_TYPE.TRAINER') }}");
        // Default trainer screen admin is online
        setAdminOnline(roomID);
        checkOnlineUser(roomID, ticketId + '-trainer-online');
    } else {
        // Default customer screen admin is online
        setAdminOnline(roomID);
        checkOnlineUser(roomID, ticketId + '-customer-online');
    }

    // Auto size the textarea 
    autosize();
    /**Get the file for base64 type */
    function getBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => resolve(reader.result);
            reader.onerror = error => reject(error);
        });
    }

    /** Send the file for customer */
    function manageCustomerData(data) {
        media = data;
        if (media != "") {
            $(".customerMsg").remove();
            send_msg();
            $("#customerFile").val(null);
            $(".customerFileName").text('');
            media = '';
            $("#customer_form").removeClass('error-form');
            $(".customer-error").text('');
        } else {
            $("#customer_form").addClass('error-form');
            $(".customer-error").text('Please enter message');
        }
    }

    /** Send the file for trainer */
    function manageTrainerData(data) {
        media = data;
        if (media != "") {
            $(".trainerMsg").remove();
            send_msg();
            $("#trainerFile").val(null);
            $(".trainerFileName").text('');
            media = '';
            $("#trainer_form").removeClass('error-form');
            $(".trainer-error").text('');
        } else {
            $("#trainer_form").addClass('error-form');
            $(".trainer-error").text('Please enter message');
        }
    }

    /** Send the file for trainer */
    function manageInterestedTrainerData(data) {
        media = data;
        if (media != "") {
            $(".interestedTrainerMsg").remove();
            send_msg();
            $("#interestedTrainerFile").val(null);
            $(".interestedTrainerFileName").text('');
            media = '';
            $("#interested_trainer_form").removeClass('error-form');
            $(".interested-trainer-error").text('');
        } else {
            $("#interested_trainer_form").addClass('error-form');
            $(".interested-trainer-error").text('Please enter message');
        }
    }

    $(document).ready(function() {
        /** Send the data message or file for customer */
        $(document).on("click", "#customerBtn", function() {
            var message = $.trim($("#customerMessage").val());
            var fileInput = document.querySelector('input[name="customerFile"]').files[0];
            if (fileInput != undefined) {
                getBase64(fileInput).then(
                    function(json) {
                        manageCustomerData(json);
                    }
                );
            } else {
                if (message != "") {
                    $(".customerMsg").remove();
                    send_msg(message);
                    $("#customerMessage").val('');
                    $("#customer_form").removeClass('error-form');
                    $(".customer-error").text('');
                } else {
                    $("#customer_form").addClass('error-form');
                    $(".customer-error").text('Please enter message');
                }
            }
        });

        /** Send the message and file trainer */
        $(document).on("click", "#trainerBtn", function() {
            var message = $.trim($("#trainerMessage").val());
            var fileInput = document.querySelector('input[name="trainerFile"]').files[0];
            if (fileInput != undefined) {
                getBase64(fileInput).then(
                    function(json) {
                        manageTrainerData(json);
                    }
                );
            } else {
                if (message != "") {
                    $("#trainerMsg").remove();
                    send_msg(message);
                    $("#trainerMessage").val('');
                    $("#trainer_form").removeClass('error-form');
                    $(".trainer-error").text('');
                } else {
                    $("#trainer_form").addClass('error-form');
                    $(".trainer-error").text('Please enter message');
                }
            }
        });

        /** Send the message and file trainer */
        $(document).on("click", "#interestedTrainerBtn", function() {
            var message = $.trim($("#interestedTrainerMessage").val());
            var fileInput = document.querySelector('input[name="interestedTrainerFile"]').files[0];
            if (fileInput != undefined) {
                getBase64(fileInput).then(
                    function(json) {
                        manageInterestedTrainerData(json);
                    }
                );
            } else {
                if (message != "") {
                    $("#interestedTrainerMsg").remove();
                    send_msg(message);
                    $("#interestedTrainerMessage").val('');
                    $("#interested_trainer_form").removeClass('error-form');
                    $(".interested-trainer-error").text('');
                } else {
                    $("#interested_trainer_form").addClass('error-form');
                    $(".interested-trainer-error").text('Please enter message');
                }
            }
        });
        scrollToEnd();
    });

    /** Send the data to socket server */
    function send_msg(message = '') {
        var sendData = {
            'ticket_id': "{{ $ticketId }}",
            'trainer_id': trainerId,
            'message': message,
            'customer_id': customerId,
            'sender_type': "{{ config('constants.SENDER_TYPE.USER') }}",
            'receiver_type': $("#receiverType").val(),
            'user_id': "{{ Auth::user()->id }}",
            'room_id': roomID,
            'file': media,
        };
        socket.emit('switchRoom', roomID, sendData);
        if (media != '') {
            setLoader();
        }

        var count = $('.chatCount-' + roomID).text();
        if (count > 0) {
            isChatRead(roomID);
        }        
    }
    /**
     * Set the windows height to bottom
     */
    $(window).on("load", function(e) {
        // $("html, body").animate({
        //     scrollTop: $(document).height()
        // }, "fast");
    });

    /** Scroll the page data */
    function scrollToEnd() {
        $(".chat-middle").animate({
            scrollTop: $('.chat-middle').prop("scrollHeight")
        }, "fast");

        // var d = $('.chat-middle');
        // d.scrollTop(d.prop("scrollHeight"));
    }

    /** Manage the customer and trainer tabbing */
    $(document).on("click", ".getActiveChat", function() {
        var value = ($(this).data('name'));
        var redirectURL = $(this).data('href') + $(this).attr('href');
        history.pushState({}, null, redirectURL);
        if (value == "{{ config('constants.SENDER_TYPE.CUSTOMER') }}") {
            trainerId = '';
            customerId = "{{ $customerId }}";
            roomID = $("#customerChannel").val();
            $("#receiverType").val("{{ config('constants.SENDER_TYPE.CUSTOMER') }}");
            /**On page load customer default is online */
            setAdminOnline(roomID);
            checkOnlineUser(roomID, ticketId + '-customer-online');
        }
        if (value == "{{ config('constants.SENDER_TYPE.TRAINER') }}") {
            customerId = '';
            trainerId = "{{ $trainerId }}";
            roomID = $("#trainerChannel").val();
            $("#receiverType").val("{{ config('constants.SENDER_TYPE.TRAINER') }}");
            window.location.reload();
            /**On page load trainer default is online */
            setAdminOnline(roomID);
            checkOnlineUser(roomID, ticketId + '-trainer-online');
        }
    });

    /** Update the socket room */
    // socket.on('updaterooms', function(newRoom) {
    //     console.log('newRoom', newRoom);
    //     roomID = newRoom;
    //     setMessage(roomID);
    //     scrollToEnd();
    // });
    // /** Set the data to interface for customer and trainer */

    /**
     * Set the customer socket data
     */
    socket.on($("#customerChannel").val(), function(data) {
        var senderMessage = filePath = '';
        console.log('socket receiver', data);
        if (data.success = true) {
            if (data.file_type != '') {
                filePath = '<a href="' + data.file_url + '" target="_blank" class="chats-img"><img src="' + data.file_type + '"/></a>';
            } else {
                filePath = '<p class="msg">' + data.message + '</p>';
            }
            if (data.sender_type == "{{ config('constants.SENDER_TYPE.USER') }}") {
                senderMessage = '<div class="sender-msg">\n\
                                    ' + filePath + '\n\
                                    \n\<span class="time">' + data.time + '</span>\n\
                                \n\</div>';
            } else {
                senderMessage = '<div class="receive-msg">\n\
                                    ' + filePath + '\n\
                                    \n\<span class="time">' + data.time + '</span>\n\
                                \n\</div>';
            }
            if (data.receiver_type == "{{ config('constants.SENDER_TYPE.CUSTOMER') }}") {
                $(".customerDiv").append(senderMessage);
                $(".customerMsg").remove();
            }
            if (data.file_type != '') {
                KTApp.unblockPage();
            }
        }

        scrollToEnd();
    });

    /**
     * Set the trainer socket data
     */
    socket.on($("#trainerChannel").val(), function(data) {
        var senderMessage = filePath = '';
        console.log('socket receiver', data);
        if (data.success = true) {
            if (data.file_type != '') {
                filePath = '<a href="' + data.file_url + '" target="_blank" class="chats-img"><img src="' + data.file_type + '"/></a>';
            } else {
                filePath = '<p class="msg">' + data.message + '</p>';
            }
            if (data.sender_type == "{{ config('constants.SENDER_TYPE.USER') }}") {
                senderMessage = '<div class="sender-msg">\n\
                                    ' + filePath + '\n\
                                    \n\<span class="time">' + data.time + '</span>\n\
                                \n\</div>';
            } else {
                senderMessage = '<div class="receive-msg">\n\
                                    ' + filePath + '\n\
                                    \n\<span class="time">' + data.time + '</span>\n\
                                \n\</div>';
            }
            if (data.receiver_type == "{{ config('constants.SENDER_TYPE.TRAINER') }}") {
                if (activeTab === 'pills-itrainer') {
                    $(".interestedTrainerDiv").append(senderMessage);
                    $(".interestedTrainerMsg").remove();
                }
                if (activeTab === 'pills-atrainer') {
                    $(".trainerDiv").append(senderMessage);
                    $(".trainerMsg").remove();
                }

            }
            if (data.file_type != '') {
                KTApp.unblockPage();
            }
        }

        scrollToEnd();
    });

    /** Emit the event for admin online/Offline */
    function setAdminOnline(roomID) {
        socket.emit('isOnline', ticketId + '-admin-online', {
            room_id: roomID,
            admin: true,
            online: true
        });
    }

    /** Emit the event for admin online/Offline */
    function checkOnlineUser(roomID, checkUser) {
        socket.emit('checkOnlineUser', {
            room_id: roomID,
            check_user: checkUser
        });
    }

    /**
     * Set the customer status online/offline
     */
    socket.on(ticketId + '-customer-online', function(data) {
        console.log('customer online', data);
        if (data.online && data.customer) {
            $(".customerStatus").text('Online');
            $(".customerStatus").removeClass('offline');
        } else {
            $(".customerStatus").text('Offline');
            $(".customerStatus").addClass('offline');
        }
    });

    /**
     * Set the customer status online/offline
     */
    socket.on(ticketId + '-trainer-online', function(data) {
        console.log('trainer online', data);
        if (data.online && data.trainer) {
            $(".trainerStatus").text('Online');
            $(".trainerStatus").removeClass('offline');
        } else {
            $(".trainerStatus").text('Offline');
            $(".trainerStatus").addClass('offline');
        }
    });

    /** To onchange event of your input file for customer */
    $(document).on("change", "#customerFile", function(e) {
        var result = setImageValidation(this.files[0]);
        $("#customerMessage").next("span").remove();
        if (result != true) {
            $("#customerBtn").prop('disabled', true);
            $("#customer_form").addClass('error-form');
            $(".customer-error").text(result);
            $(".customerFileName").text('');
        } else {
            $("#customerBtn").prop('disabled', false);
            $(".customerFileName").text(this.files[0].name);
            $("#customer_form").removeClass('error-form');
            $(".customer-error").text('');
        }
    });

    /** To onchange event of your input file for trainer */
    $('#trainerFile').on('change', function(e) {
        var result = setImageValidation(this.files[0]);
        $("#trainerMessage").next("span").remove();
        if (result != true) {
            $("#trainer_form").addClass('error-form');
            $(".trainer-error").text(result);
            $("#trainerBtn").prop('disabled', true);
            $(".trainerFileName").text('');
        } else {
            $("#trainerBtn").prop('disabled', false);
            $(".trainerFileName").text(this.files[0].name);
            $("#trainer_form").removeClass('error-form');
            $(".trainer-error").text('');
        }
    });

    /** To onchange event of your input file for interested trainer */
    $('#interestedTrainerFile').on('change', function(e) {
        var result = setImageValidation(this.files[0]);
        $("#interestedTrainerMessage").next("span").remove();
        if (result != true) {
            $("#interested_trainer_form").addClass('error-form');
            $(".interested-trainer-error").text(result);
            $("#interestedTrainerBtn").prop('disabled', true);
            $(".interestedTrainerFileName").text('');
        } else {
            $("#interested_trainer_form").removeClass('error-form');
            $(".interested-trainer-error").text('');
            $("#interestedTrainerBtn").prop('disabled', false);
            $(".interestedTrainerFileName").text(this.files[0].name);
        }
    });


    /** Set the image validation */
    function setImageValidation(file) {
        var ext = file.type.split('/').pop().toLowerCase();
        if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg', 'msword', 'pdf', 'vnd.openxmlformats-officedocument.wordprocessingml.document']) == -1) {
            return "Only allowed file types: png, jpg, jpeg, pdf or doc.";
        } else if (file.size >= 5000000) {
            return "File size must be less than 5MB";
        } else {
            return true;
        }
    }

    /** On key up for textarea remove the validation */
    $(document).on("keyup", "#customerMessage", function() {
        $("#customerMessage").next("span").remove();
        $("#customerBtn").prop('disabled', false);
        $("#customerFile").val(null);
        $("#customer_form").removeClass('error-form');
        $(".customer-error").text('');
    });

    /** On key up for textarea remove the validation */
    $(document).on("keyup", "#trainerMessage", function() {
        $("#trainerMessage").next("span").remove();
        $("#trainerBtn").prop('disabled', false);
        $("#trainerFile").val(null);
        $("#trainer_form").removeClass('error-form');
        $(".trainer-error").text('');
    });

    /** On key up for interested textarea remove the validation */
    $(document).on("keyup", "#interestedTrainerMessage", function() {
        $("#interestedTrainerMessage").next("span").remove();
        $("#interestedTrainerBtn").prop('disabled', false);
        $("#interestedTrainerFile").val(null);
        $("#interested_trainer_form").removeClass('error-form');
        $(".interested-trainer-error").text('');
    });

    /**
     * Set the textrea size and auto height
     */
    function autosize() {
        var text = $('.example-preview .chat-footer').find('textarea');
        text.each(function() {
            $(this).attr('rows', 1);
            resize($(this));
        });
        text.on('input', function() {
            resize($(this));
        });

        $('#customerBtn').click(function() {
            text.css('height', 'auto');
        });
        $('#trainerBtn').click(function() {
            text.css('height', 'auto');
        });
        $('#interestedTrainerBtn').click(function() {
            text.css('height', 'auto');
        });
    }

    /**
     * Set the size of text area
     */
    function resize($text) {
        var scrollHeight = ($text[0].scrollHeight) ? $text[0].scrollHeight : '36';
        $text.css('height', 'auto');
        $text.css('height', scrollHeight + 'px');
    }

    /**
     * Read the admin chat and update the un read count
     */
    function isChatRead(room_id) {
        $.ajax({
            type: 'POST',
            url: "{{ route('chat.read') }}",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: {
                room_id: room_id
            },
            success: function(data) {
                $('.chatCount-' + data).remove();
            },
        });
    }
</script>


@endsection