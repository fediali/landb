@extends('core/base::layouts.master')

@section('content')
    <div class="p-3 bg-white">
        <div class="clearfix"></div>
        <div id="main">

            <div class="container">

            <!-- <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-header">Customers</div>
                            <div class="card-body">
                                @if (!count($customers))
                <p>No customers</p>
@else
                <ul class="list-group list-group-flush">

@foreach ($customers as $row)
                    <a href="{{ route('chating.messages.chat', [ 'ids' => auth()->user()->id  . '-' . $row->id ]) }}" class="list-group-item list-group-item-action">{{ $row->name }}</a>
                                        @endforeach
                    </ul>
@endif
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-body text-center">
                    <p class="font-weight-bold">You don’t have a chat selected</p>
                    <p>Choose a user to continue an existing chat or start a new one.</p>
                </div>
            </div>
        </div>
    </div> -->

                <div class="row app-one">
                    <div class="col-sm-4 side">
                        <div class="side-one">
                            <div class="row heading">
                                <div class="col-sm-11 col-xs-11 heading-avatar">
                                    <div class="d-flex heading-avatar-icon">
                                        <img src="https://bootdey.com/img/Content/avatar/avatar1.png">
                                        <p style="margin-top: 10px; margin-left: 10px;">Customers</p>
                                    </div>
                                </div>

                            </div>

                            <div class="row searchBox">
                                <div class="col-sm-12 searchBox-inner">
                                    <div class="form-group has-feedback">
                                        <input id="searchText" type="text" class="form-control" name="searchText"
                                               placeholder="Search">
                                        <span class="glyphicon glyphicon-search form-control-feedback"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row sideBar nav nav-tabs" id="myTab" role="tablist">
                                @if (!count($customers))

                                    <p>No customers</p>
                                @else

                                    @foreach ($customers as $customer)
                                    <div class="nav-item" role="presentation">   
                                        <a href="{{ route('chating.messages.chat', [ 'ids' => auth()->user()->id  . '-' . $customer->id ]) }}"
                                           class="list-group-item list-group-item-action">
                                            <div
                                                class="row {{  (!$customer->chat->isEmpty()) ? (get_chat($customer->chat[0]->message_sid,auth()->user()->twilio_number ) > $customer->chat[0]->chat_count) ? 'msg_unread':'' : '' }} sideBar-body">
                                                <div class="col-sm-3 col-xs-3 sideBar-avatar">
                                                    <div class="avatar-icon">
                                                        <img src="https://bootdey.com/img/Content/avatar/avatar1.png">
                                                    </div>
                                                </div>
                                                <div class="col-sm-9 col-xs-9 sideBar-main">
                                                    <div class="row">
                                                        <div class="col-sm-8 col-xs-8 sideBar-name">
                                <span class="name-meta">{{$customer->name}}
                                </span>
                                                        </div>
                                                        <div class="col-sm-4 col-xs-4 pull-right sideBar-time">
                                <span class="time-meta pull-right">
                                </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if(!$customer->chat->isEmpty())
                                                    @php
                                                        $chatCnt = get_chat($customer->chat[0]->message_sid, auth()->user()->twilio_number);
                                                    @endphp
                                                    @if( $chatCnt > $customer->chat[0]->chat_count )
                                                        <p class="msg-count">{{$chatCnt - $customer->chat[0]->chat_count }}</p>
                                                    @endif
                                                @endisset

                                            </div>
                                        </a>
                                     </div>
                                    @endforeach
                                @endif

                            </div>


                        </div>

                        <div class="side-two">
                            <div class="row newMessage-heading">
                                <div class="row newMessage-main">
                                    <div class="col-sm-2 col-xs-2 newMessage-back">
                                        <i class="fa fa-arrow-left" aria-hidden="true"></i>
                                    </div>
                                    <div class="col-sm-10 col-xs-10 newMessage-title">
                                        New Chat
                                    </div>
                                </div>
                            </div>

                            <div class="row composeBox">
                                <div class="col-sm-12 composeBox-inner">
                                    <div class="form-group has-feedback">
                                        <input id="composeText" type="text" class="form-control" name="searchText"
                                               placeholder="Search People">
                                        <span class="glyphicon glyphicon-search form-control-feedback"></span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-sm-8 conversation">
                        <div class="row heading">
                            <div class="col-sm-2 col-md-1 col-xs-3 heading-avatar">
                                <div class="heading-avatar-icon">
                                </div>
                            </div>
                            <div class="col-sm-10 col-xs-7 heading-name">
                                <a class="heading-name-meta">
                                </a>
                            </div>
                            <div class="col-sm-1 col-xs-1 text-right show-detail heading-dot pull-right">
                            <i class="fa fa-ellipsis-v fa-2x  pull-right" aria-hidden="true"></i>
                            </div>
                        </div>

                        

                        <div class="row message" id="conversation">
                            <div class="row message-previous">
                                <div class="col-sm-12 previous">
                                    <a class="mt-3" onclick="previous(this)" id="ankitjain28" name="20">
                                        You don’t have a chat selected
                                    </a>
                                    <p class="mt-3">Choose a user to continue an existing chat or start a new one.</p>
                                </div>
                            </div>

                        </div>

                        <div class="side-three">
                                <div class="row newMessage-heading">
                                <div class="row newMessage-main">
                                    <div class="col-sm-2 col-xs-2 detail-back newMessage-back">
                                    <i class="fa fa-arrow-left" aria-hidden="true"></i>
                                    </div>
                                    <div class="col-sm-10 col-xs-10 newMessage-title">
                                    Customer Name
                                    </div>
                                </div>
                                </div>
    

                            <div class="row compose-sideBar">
                                <div class="row sideBar-body"> 
                                    <div class="col-sm-12 col-xs-12 sideBar-main">
                                    <div class="row">
                                        <div class="col-sm-12 col-xs-12 text-center sideBar-name">
                                        <span class="name-meta"> Phone Number: <b>+123 456789</b>
                                        </span>
                                        <span class="name-meta"> Email: <b>demo@gmail.com</b>
                                        </span>
                                        </div> 
                                    </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                       
                    </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@stop


@section('javascript')
<script type="text/javascript">
	$(function(){
    
	 $(".show-detail").click(function() {
      $(".side-three").css({
        "right": "0"
      });
    });
    $(".detail-back").click(function() {
      $(".side-three").css({
        "right": "-100%"
      });
    });
})
</script>
@endsection
