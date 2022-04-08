<div class="tab-pane fade" id="pills-atrainer" role="tabpanel" aria-labelledby="pills-atrainer-tab">
    <div class="row">
        <div class="col-md-3">
            <div class="chat-left">
                <h3>{{ __('Assigned Trainer') }}</h3>
                <ul>
                    <li>
                        <a href="javascript:void(0)" class="chat-name d-flex justify-content-between align-items-center active">
                            <div class="d-flex align-items-center chat-name-left">
                                <div class="chat-img">
                                    @if($avatar)
                                    <img alt="{{ $fullName }}" src="{{ asset($avatar) }}">
                                    @endif
                                </div>
                                <span>{{ $fullName ?? ''}}</span>
                            </div>
                            @php 
                                $trainerChatRoom = '';
                                if($interestedTrainer->trainer){
                                    $trainerChatRoom = $interestedTrainer->trainer->chatRooms->first();
                                }
                            @endphp
                            @if($trainerChatRoom && $trainerChatRoom->chat_messages_count)
                                <span class="chat-number d-flex justify-content-center align-items-center chatCount-{{ $trainerChatRoom->room_id }}">{{ $trainerChatRoom->chat_messages_count ?? '' }}</span>
                            @endif
                        </a>
                    </li>

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
                        <span>{{ $fullName ?? ''}}</span>
                        <span class="online trainerStatus"><i class="green-dot"></i>Offline</span>
                    </div>
                </div>
                @isset($chatMessages['trainerData'])
                    @php $chatData = $chatMessages['trainerData']['chatMessages']; @endphp
                @endisset
                @php $activeDiv = 'trainerDiv'; $notFoundMsg = 'trainerMsg'; @endphp                
                @include('chats._form')
                <div class="chat-footer">
                    <form method="post" id="trainer_form" class="d-flex justify-content-end align-items-center">
                        <textarea id="trainerMessage" class="form-control message mr-3" placeholder="Type a message" name="message"></textarea>
                        <div class="mr-3 file-main">
                            <a href="#" class="btn btn-clean btn-icon btn-md file-icon">
                                <i class="flaticon2-photograph icon-lg"></i>
                                <input type="file" name="trainerFile" id="trainerFile" class="file-input">
                            </a>
                            <span class="text-muted trainerFileName"></span>
                        </div>
                        <div class="send-btn">
                            <button id="trainerBtn" type="button" class="btn btn-primary btn-md text-uppercase font-weight-bold chat-send py-2 px-6" data-toggle="tooltip" data-theme="dark" title="{{ __('Send') }}">{{ __('Send') }}</button>
                        </div>
                        <span class="error-text trainer-error"></span>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>