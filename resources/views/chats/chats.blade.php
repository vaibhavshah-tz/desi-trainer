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
        <a class="nav-link active" id="pills-customer-tab" data-toggle="pill" href="#pills-customer" role="tab" aria-controls="pills-customer" aria-selected="true">Customer</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="pills-atrainer-tab" data-toggle="pill" href="#pills-atrainer" role="tab" aria-controls="pills-atrainer" aria-selected="false">Assigned Trainer</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="pills-itrainer-tab" data-toggle="pill" href="#pills-itrainer" role="tab" aria-controls="pills-itrainer" aria-selected="false">Interested Trainer</a>
    </li>
</ul>
<div class="card card-custom chat-main-section">
    <!--begin::Example-->
    <div class="chat-main">
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-customer" role="tabpanel" aria-labelledby="pills-customer-tab">
                <div class="row">
                    <div class="col-md-3">
                        <div class="chat-left">
                            <h3>Customer</h3>
                            <ul>
                                <li>
                                    <a href="javascript:void(0)" class="chat-name d-flex justify-content-between align-items-center active">
                                        <div class="d-flex align-items-center chat-name-left">
                                            <div class="chat-img">
                                                <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                            </div>
                                            <span>Viola Pena</span>
                                        </div>
                                        <span class="chat-number d-flex justify-content-center align-items-center">10</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="chat-name d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center chat-name-left">
                                            <div class="chat-img">
                                                <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                            </div>
                                            <span>Viola Pena</span>
                                        </div>
                                        <span class="chat-number d-flex justify-content-center align-items-center">10</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="chat-name d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center chat-name-left">
                                            <div class="chat-img">
                                                <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                            </div>
                                            <span>Viola Pena</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="chat-name d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center chat-name-left">
                                            <div class="chat-img">
                                                <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                            </div>
                                            <span>Viola Pena</span>
                                        </div>
                                        <span class="chat-number d-flex justify-content-center align-items-center">10</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="chat-right">
                            <div class="chat-header d-flex align-items-center chat-name-left chat-name">
                                <div class="chat-img">
                                    <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                </div>
                                <div class="d-flex flex-column">
                                    <span>Viola Pena customer</span>
                                    <span class="online"><i class="green-dot"></i>Online</span>
                                </div>  
                            </div>
                            <div class="chat-middle">
                                <div class="chating-div">
                                    <div class="sender-msg">
                                        <p class="msg">Lorem ipsum lorem</p>
                                        <span class="time">03 Feb 2021, 01:21 PM</span>
                                    </div>
                                    <div class="receive-msg">
                                        <p class="msg">Lorem ipsum lorem</p>
                                        <span class="time">03 Feb 2021, 01:21 PM</span>
                                    </div>
                                    <div class="sender-msg">
                                        <a href="javascript:void(0)" class="chats-img">
                                            <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                        </a>
                                        <span class="time">03 Feb 2021, 01:21 PM</span>
                                    </div>
                                    <div class="receive-msg">
                                        <a href="javascript:void(0)" class="chats-img">
                                            <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                        </a>
                                        <p class="msg">Lorem ipsum lorem</p>
                                        <span class="time">03 Feb 2021, 01:21 PM</span>
                                    </div>
                                    <div class="receive-msg">
                                        <p class="msg">Lorem ipsum lorem</p>
                                        <span class="time">03 Feb 2021, 01:21 PM</span>
                                    </div>
                                    <div class="sender-msg">
                                        <p class="msg">Lorem ipsum lorem Lorem ipsum lorem Lorem ipsum lorem Lorem ipsum lorem Lorem ipsum lorem Lorem ipsum lorem</p>
                                        <p class="msg">Lorem ipsum lorem Lorem ipsum lorem Lorem</p>
                                        <span class="time">03 Feb 2021, 01:21 PM</span>
                                    </div>
                                    <div class="receive-msg">
                                        <p class="msg">Lorem ipsum lorem Lorem ipsum lorem Lorem ipsum lorem Lorem ipsum lorem Lorem ipsum lorem Lorem ipsum lorem</p>
                                        <span class="time">03 Feb 2021, 01:21 PM</span>
                                    </div>
                                </div>
                            </div>
                            <div class="chat-footer">
                                <form method="post" id="upload_form" class="d-flex justify-content-end align-items-center">
                                    <textarea id="customerMessage" class="form-control message mr-3" placeholder="Type a message" name="message"></textarea>
                                    <div class="mr-3 file-main">
                                        <a href="#" class="btn btn-clean btn-icon btn-md file-icon">
                                            <i class="flaticon2-photograph icon-lg"></i>
                                            <input type="file" name="customerFile" id="customerFile" class="file-input">
                                        </a>
                                        <span class="text-muted customerFileName"></span>
                                    </div>
                                    <div class="send-btn">
                                        <button id="customerBtn" type="button" class="btn btn-primary btn-md text-uppercase font-weight-bold chat-send py-2 px-6" data-toggle="tooltip" data-theme="dark" title="{{ __('Send') }}">{{ __('Send') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="pills-atrainer" role="tabpanel" aria-labelledby="pills-atrainer-tab">
                <div class="row">
                    <div class="col-md-3">
                        <div class="chat-left">
                            <h3>Assigned Trainer</h3>
                            <ul>
                                <li>
                                    <a href="javascript:void(0)" class="chat-name d-flex justify-content-between align-items-center active">
                                        <div class="d-flex align-items-center chat-name-left">
                                            <div class="chat-img">
                                                <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                            </div>
                                            <span>Viola Pena</span>
                                        </div>
                                        <span class="chat-number d-flex justify-content-center align-items-center">10</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="chat-name d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center chat-name-left">
                                            <div class="chat-img">
                                                <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                            </div>
                                            <span>Viola Pena</span>
                                        </div>
                                        <span class="chat-number d-flex justify-content-center align-items-center">10</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="chat-name d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center chat-name-left">
                                            <div class="chat-img">
                                                <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                            </div>
                                            <span>Viola Pena</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="chat-name d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center chat-name-left">
                                            <div class="chat-img">
                                                <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                            </div>
                                            <span>Viola Pena</span>
                                        </div>
                                        <span class="chat-number d-flex justify-content-center align-items-center">10</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="chat-name d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center chat-name-left">
                                            <div class="chat-img">
                                                <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                            </div>
                                            <span>Viola Pena</span>
                                        </div>
                                        <span class="chat-number d-flex justify-content-center align-items-center">10</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="chat-name d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center chat-name-left">
                                            <div class="chat-img">
                                                <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                            </div>
                                            <span>Viola Pena</span>
                                        </div>
                                        <span class="chat-number d-flex justify-content-center align-items-center">10</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="chat-name d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center chat-name-left">
                                            <div class="chat-img">
                                                <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                            </div>
                                            <span>Viola Pena</span>
                                        </div>
                                        <span class="chat-number d-flex justify-content-center align-items-center">10</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="chat-name d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center chat-name-left">
                                            <div class="chat-img">
                                                <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                            </div>
                                            <span>Viola Pena</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="chat-name d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center chat-name-left">
                                            <div class="chat-img">
                                                <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                            </div>
                                            <span>Viola Pena</span>
                                        </div>
                                        <span class="chat-number d-flex justify-content-center align-items-center">10</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="chat-name d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center chat-name-left">
                                            <div class="chat-img">
                                                <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                            </div>
                                            <span>Viola Pena</span>
                                        </div>
                                        <span class="chat-number d-flex justify-content-center align-items-center">10</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="chat-name d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center chat-name-left">
                                            <div class="chat-img">
                                                <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                            </div>
                                            <span>Viola Pena</span>
                                        </div>
                                        <span class="chat-number d-flex justify-content-center align-items-center">10</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="chat-name d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center chat-name-left">
                                            <div class="chat-img">
                                                <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                            </div>
                                            <span>Viola Pena</span>
                                        </div>
                                        <span class="chat-number d-flex justify-content-center align-items-center">10</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="chat-name d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center chat-name-left">
                                            <div class="chat-img">
                                                <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                            </div>
                                            <span>Viola Pena</span>
                                        </div>
                                        <span class="chat-number d-flex justify-content-center align-items-center">10</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="chat-name d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center chat-name-left">
                                            <div class="chat-img">
                                                <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                            </div>
                                            <span>Viola Pena</span>
                                        </div>
                                        <span class="chat-number d-flex justify-content-center align-items-center">10</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="chat-right">
                            <div class="chat-header d-flex align-items-center chat-name-left chat-name">
                                <div class="chat-img">
                                    <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                </div>
                                <div class="d-flex flex-column">
                                    <span>Viola Pena assign trainer</span>
                                    <span class="online"><i class="green-dot"></i>Online</span>
                                </div>  
                            </div>
                            <div class="chat-middle">
                                <div class="chating-div">
                                    <div class="sender-msg">
                                        <p class="msg">Lorem ipsum lorem</p>
                                        <span class="time">03 Feb 2021, 01:21 PM</span>
                                    </div>
                                    <div class="receive-msg">
                                        <p class="msg">Lorem ipsum lorem</p>
                                        <span class="time">03 Feb 2021, 01:21 PM</span>
                                    </div>
                                    <div class="sender-msg">
                                        <a href="javascript:void(0)" class="chats-img">
                                            <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                        </a>
                                        <span class="time">03 Feb 2021, 01:21 PM</span>
                                    </div>
                                    <div class="receive-msg">
                                        <a href="javascript:void(0)" class="chats-img">
                                            <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                        </a>
                                        <p class="msg">Lorem ipsum lorem</p>
                                        <span class="time">03 Feb 2021, 01:21 PM</span>
                                    </div>
                                    <div class="receive-msg">
                                        <p class="msg">Lorem ipsum lorem</p>
                                        <span class="time">03 Feb 2021, 01:21 PM</span>
                                    </div>
                                    <div class="sender-msg">
                                        <p class="msg">Lorem ipsum lorem Lorem ipsum lorem Lorem ipsum lorem Lorem ipsum lorem Lorem ipsum lorem Lorem ipsum lorem</p>
                                        <p class="msg">Lorem ipsum lorem Lorem ipsum lorem Lorem</p>
                                        <span class="time">03 Feb 2021, 01:21 PM</span>
                                    </div>
                                    <div class="receive-msg">
                                        <p class="msg">Lorem ipsum lorem Lorem ipsum lorem Lorem ipsum lorem Lorem ipsum lorem Lorem ipsum lorem Lorem ipsum lorem</p>
                                        <span class="time">03 Feb 2021, 01:21 PM</span>
                                    </div>
                                </div>
                            </div>
                            <div class="chat-footer">
                                <form method="post" id="upload_form" class="d-flex justify-content-end align-items-center">
                                    <textarea id="customerMessage" class="form-control message mr-3" placeholder="Type a message" name="message"></textarea>
                                    <div class="mr-3 file-main">
                                        <a href="#" class="btn btn-clean btn-icon btn-md file-icon">
                                            <i class="flaticon2-photograph icon-lg"></i>
                                            <input type="file" name="customerFile" id="customerFile" class="file-input">
                                        </a>
                                        <span class="text-muted customerFileName">company-profile-img01.jpg</span>
                                    </div>
                                    <div class="send-btn">
                                        <button id="customerBtn" type="button" class="btn btn-primary btn-md text-uppercase font-weight-bold chat-send py-2 px-6" data-toggle="tooltip" data-theme="dark" title="{{ __('Send') }}">{{ __('Send') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="pills-itrainer" role="tabpanel" aria-labelledby="pills-itrainer-tab"><div class="row">
                    <div class="col-md-3">
                        <div class="chat-left">
                            <h3>Interested Trainer</h3>
                            <ul>
                                <li>
                                    <a href="javascript:void(0)" class="chat-name d-flex justify-content-between align-items-center active">
                                        <div class="d-flex align-items-center chat-name-left">
                                            <div class="chat-img">
                                                <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                            </div>
                                            <span>Viola Pena</span>
                                        </div>
                                        <span class="chat-number d-flex justify-content-center align-items-center">10</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="chat-name d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center chat-name-left">
                                            <div class="chat-img">
                                                <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                            </div>
                                            <span>Viola Pena</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="chat-name d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center chat-name-left">
                                            <div class="chat-img">
                                                <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                            </div>
                                            <span>Viola Pena</span>
                                        </div>
                                        <span class="chat-number d-flex justify-content-center align-items-center">10</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="chat-right">
                            <div class="chat-header d-flex align-items-center chat-name-left chat-name">
                                <div class="chat-img">
                                    <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                </div>
                                <div class="d-flex flex-column">
                                    <span>Viola Pena interested</span>
                                    <span class="online"><i class="green-dot"></i>Online</span>
                                </div>  
                            </div>
                            <div class="chat-middle">
                                <div class="chating-div">
                                    <div class="sender-msg">
                                        <p class="msg">Lorem ipsum lorem</p>
                                        <span class="time">03 Feb 2021, 01:21 PM</span>
                                    </div>
                                    <div class="receive-msg">
                                        <a href="javascript:void(0)" class="chats-img">
                                            <img src="{{asset('media/stock-900x600/3.jpg')}}"/>
                                        </a>
                                        <p class="msg">Lorem ipsum lorem</p>
                                        <span class="time">03 Feb 2021, 01:21 PM</span>
                                    </div>
                                    <div class="receive-msg">
                                        <p class="msg">Lorem ipsum lorem</p>
                                        <span class="time">03 Feb 2021, 01:21 PM</span>
                                    </div>
                                    <div class="sender-msg">
                                        <p class="msg">Lorem ipsum lorem Lorem ipsum lorem Lorem ipsum lorem Lorem ipsum lorem Lorem ipsum lorem Lorem ipsum lorem</p>
                                        <p class="msg">Lorem ipsum lorem Lorem ipsum lorem Lorem</p>
                                        <span class="time">03 Feb 2021, 01:21 PM</span>
                                    </div>
                                </div>
                            </div>
                            <div class="chat-footer">
                                <form method="post" id="upload_form" class="d-flex justify-content-end align-items-center error-form">
                                    <textarea id="customerMessage" class="form-control message mr-3" placeholder="Type a message" name="message"></textarea>
                                    <div class="mr-3 file-main">
                                        <a href="#" class="btn btn-clean btn-icon btn-md file-icon">
                                            <i class="flaticon2-photograph icon-lg"></i>
                                            <input type="file" name="customerFile" id="customerFile" class="file-input"> 
                                        </a>
                                        <span class="text-muted customerFileName"></span> 
                                    </div>
                                    <div class="send-btn">
                                        <button id="customerBtn" type="button" class="btn btn-primary btn-md text-uppercase font-weight-bold chat-send py-2 px-6" data-toggle="tooltip" data-theme="dark" title="{{ __('Send') }}">{{ __('Send') }}</button>
                                    </div>
                                    <span class="error-text">Please enter message</span>
                                </form>
                            </div>
                        </div>
                    </div>
                </div></div>
        </div>
    </div>
    

</div>
<!--end::Card-->
@endsection

@section('styles')
<!-- <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" /> -->
@endsection

{{-- Scripts Section --}}
@section('scripts')

@endsection