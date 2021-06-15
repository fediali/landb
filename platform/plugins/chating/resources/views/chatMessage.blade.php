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
                                        @foreach ($customers as $customer)
                                            <a href="{{ route('chating.messages.chat', [ 'ids' => auth()->user()->id  . '-' . $customer->id ]) }}" class="list-group-item list-group-item-action">{{ $customer->name }}</a>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9" id="chat-main">
                        <chat-component :auth-user="{{ auth()->user() }}" :other-user="{{ $otherUser }}" :messages="{{$messages}}" :sid="{{$sid}}"></chat-component>
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
                            <input id="searchText" type="text" class="form-control" name="searchText" placeholder="Search">
                            <span class="glyphicon glyphicon-search form-control-feedback"></span>
                            </div>
                        </div>
                        </div>

                        <div class="row sideBar"> 
  
                        @if (!count($customers))
                            <p>No customers</p>
                        @else
                        @foreach ($customers as $customer)
                        <a href="{{ route('chating.messages.chat', [ 'ids' => auth()->user()->id  . '-' . $customer->id ]) }}">
                        <div class="row sideBar-body">
                            <div class="col-sm-3 col-xs-3 sideBar-avatar">
                            <div class="avatar-icon">
                                <img src="https://bootdey.com/img/Content/avatar/avatar1.png">
                            </div>
                            </div>
                            <div class="col-sm-9 col-xs-9 sideBar-main">
                            <div class="row">
                                <div class="col-sm-8 col-xs-8 sideBar-name">
                                <span class="name-meta">{{ $customer->name }}
                                </span>
                                </div>
                                <div class="col-sm-4 col-xs-4 pull-right sideBar-time">
                                <span class="time-meta pull-right">
                                </span>
                                </div>
                            </div>
                            </div>
                        </div>
                        </a>
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
                            <input id="composeText" type="text" class="form-control" name="searchText" placeholder="Search People">
                            <span class="glyphicon glyphicon-search form-control-feedback"></span>
                            </div>
                        </div>
                        </div>

                        <div class="row compose-sideBar">
                        <div class="row sideBar-body">
                            <div class="col-sm-3 col-xs-3 sideBar-avatar">
                            <div class="avatar-icon">
                                <img src="https://bootdey.com/img/Content/avatar/avatar1.png">
                            </div>
                            </div>
                            <div class="col-sm-9 col-xs-9 sideBar-main">
                            <div class="row">
                                <div class="col-sm-8 col-xs-8 sideBar-name">
                                <span class="name-meta">John Doe
                                </span>
                                </div>
                                <div class="col-sm-4 col-xs-4 pull-right sideBar-time">
                                <span class="time-meta pull-right">18:18
                                </span>
                                </div>
                            </div>
                            </div>
                        </div>
                            <div class="row sideBar-body">
                            <div class="col-sm-3 col-xs-3 sideBar-avatar">
                            <div class="avatar-icon">
                                <img src="https://bootdey.com/img/Content/avatar/avatar5.png">
                            </div>
                            </div>
                            <div class="col-sm-9 col-xs-9 sideBar-main">
                            <div class="row">
                                <div class="col-sm-8 col-xs-8 sideBar-name">
                                <span class="name-meta">John Doe
                                </span>
                                </div>
                                <div class="col-sm-4 col-xs-4 pull-right sideBar-time">
                                <span class="time-meta pull-right">18:18
                                </span>
                                </div>
                            </div>
                            </div>
                        </div> 
                        </div>
                    </div>
                    </div>

                    <div class="col-sm-8 conversation" id="chat-main">
                    <chat-component :auth-user="{{ auth()->user() }}" :other-user="{{ $otherUser }}" :messages="{{$messages}}" :sid="{{$sid}}"></chat-component>
                   
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop


@section('javascript')
    <script src="https://media.twiliocdn.com/sdk/js/chat/v3.3/twilio-chat.min.js"></script>
    <script></script>
@endsection
