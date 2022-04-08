<div class="tab-pane fade" id="pills-itrainer" role="tabpanel" aria-labelledby="pills-itrainer-tab">
    <div class="row">
        <div class="col-md-3">
            <div class="chat-left">
                <h3>{{ __('Interested Trainer') }}</h3>
                <ul>
                    @if($interestedTrainer->interestedTrainers->count() > 0)
                        @foreach($interestedTrainer->interestedTrainers as $key => $value)
                            <li>
                                <a href='{{ url("tickets/$ticketId/chat/$value->id#pills-itrainer") }}' class="chat-name d-flex justify-content-between align-items-center {{ (\Request::route('trainer_id') == $value->id) ? 'active' : '' }}">
                                    <div class="d-flex align-items-center chat-name-left">
                                        <div class="chat-img">
                                            <img alt="{{ $value->full_name ?? '' }}" src="{{ asset($value->avatar_url) }}">
                                        </div>
                                        <span>{{ $value->full_name ?? '' }}</span>
                                    </div>
                                    
                                    @if($value->chatRooms->first() && $value->chatRooms->first()->chat_messages_count)
                                        <span class="chat-number d-flex justify-content-center align-items-center chatCount-{{ $value->chatRooms->first()->room_id }}">{{ $value->chatRooms->first()->chat_messages_count ?? '' }}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
        <div class="col-md-9">
            <div class="chat-right">
                <div class="chat-header d-flex align-items-center chat-name-left chat-name">
                    <div class="chat-img">
                        @if($avatar)
                            <img alt="{{ $fullName }}" src="{{ asset($avatar) }}">
                        @endif
                    </div>
                    <div class="d-flex flex-column">
                        <span>{{ $fullName ?? '' }}</span>
                        <span class="online trainerStatus"><i class="green-dot"></i>Offline</span>
                    </div>
                </div>
                @isset($chatMessages['trainerData'])
                    @php $chatData = $chatMessages['trainerData']['chatMessages']; @endphp
                @endisset
                @php $activeDiv = 'interestedTrainerDiv'; $notFoundMsg = 'interestedTrainerMsg'; @endphp
                @include('chats._form')
                <div class="chat-footer">
                    <form method="post" id="interested_trainer_form" class="d-flex justify-content-end align-items-center">
                        <textarea id="interestedTrainerMessage" class="form-control message mr-3" placeholder="Type a message" name="message"></textarea>
                        <div class="mr-3 file-main">
                            <a href="#" class="btn btn-clean btn-icon btn-md file-icon">
                                <i class="flaticon2-photograph icon-lg"></i>
                                <input type="file" name="interestedTrainerFile" id="interestedTrainerFile" class="file-input">
                            </a>
                            <span class="text-muted interestedTrainerFileName"></span>
                        </div>
                        <div class="send-btn">
                            <button id="interestedTrainerBtn" type="button" class="btn btn-primary btn-md text-uppercase font-weight-bold chat-send py-2 px-6" data-toggle="tooltip" data-theme="dark" title="{{ __('Send') }}">{{ __('Send') }}</button>
                        </div>
                        <span class="error-text interested-trainer-error"></span>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>