<div class="chat-middle">
    <div class="chating-div {{ $activeDiv }}">       
        @isset($chatData)
            @foreach($chatData as $chatKey => $chatValue)
                @if($chatValue['sender_type'] == config('constants.SENDER_TYPE.USER'))
                    <div class="sender-msg">
                        @if($chatValue['message'])
                        <!-- // nl2br -->
                        <p class="msg"> {!! $chatValue['message'] ?? 'N/A' !!}</p>
                        @else
                        <a href="{{ $chatValue['file_url'] }}" target="_blank" class="chats-img"><img src="{{ $chatValue['file_type'] }}" /></a>
                        @endif
                        <span class="time">{{ $chatValue['time'] ?? '' }}</span>
                    </div>
                @else
                    <div class="receive-msg">
                        @if($chatValue['message'])
                        <p class="msg"> {!! $chatValue['message'] ?? 'N/A' !!}</p>
                        @else
                        <a href="{{ $chatValue['file_url'] }}" target="_blank" class="chats-img"><img src="{{ $chatValue['file_type'] }}" /></a>
                        @endif
                        <span class="time">{{ $chatValue['time'] ?? '' }}</span>
                    </div>
                @endif
            @endforeach
            @if(count($chatData) == 0)
                <div class="no-found-msg {{ $notFoundMsg }}">
                    {{ __('No message found') }}
                </div>
            @endif
        @else
        <div class="no-found-msg {{ $notFoundMsg }}">
            {{ __('No message found') }}
        </div>
        @endisset
    </div>
</div>