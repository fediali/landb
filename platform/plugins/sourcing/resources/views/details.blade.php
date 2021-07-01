@extends('core/base::layouts.master')

@section('content')

    <div class="p-3 bg-white" >
        <div class="clearfix"></div>
        <div id="main">

            <div class="row">
                <div class="col-lg-12 mb-3">
                    <h5 class="order-detail">SOURCING DETAIL </h5>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <p class="m-0 heading">Name</p>
                    <p class="m-3">{{$sourcing->name}}</p>
                </div>
                <div class="col-lg-4">
                    <p class="m-0 heading">Notes</p>
                    <p class="m-3">{{$sourcing->notes}}</p>
                </div>
                <div class="col-lg-4">
                    <p class="m-0 heading">Status</p>
                    <p class="m-3">{{$sourcing->status}}</p>
                </div>
            </div><hr>
            <?php
                $images = json_decode($sourcing->file);
            ?>
            <div class="row">
                <div class="col-lg-12 mb-3">
                    <h5 class="order-detail">SOURCING IMAGES </h5>
                </div>
                <div class="col-lg-3"></div>
                <div class="col-lg-6">
                    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators">
                            @foreach($images as $image)
                                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="@if($loop->first) active @endif"></li>
                            @endforeach
                        </ol>
                        <div class="carousel-inner">
                            @foreach($images as $image)
                                <div class="carousel-item @if($loop->first) active @endif">
                                    <img height="400px" style="object-fit: cover" class="d-block w-100" src="{{ asset('storage/'.$image) }}" alt="{{$loop->iteration}} slide">
                                </div>
                            @endforeach
                        </div>
                        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="p-3 bg-white" >
        <div class="clearfix"></div>
        <div id="main">
            <hr><div class="row justify-content-center h-100">

                <div class="col-md-11 col-xl-11 messageboard">
                    <div class="card">
                        <div class="card-header msg_head">
                            <div class="d-flex bd-highlight">
                                <div class="img_cont">
                                    <img height="70" width="70" src="{{ asset('images/chat-bubble.png') }}" class="user_img">
                                </div>
                                <div class="user_info">
                                    <span>Add Discussion</span>
                                    <p>{{ count($sourcing->childs) }} Comment(s)</p>
                                </div>

                            </div>

                        </div>
                        <div class="card-body msg_card_body">

                            @foreach($sourcing->childs as $comment)
                                <div class="d-flex justify-content-start mb-4">
                                    <div class="img_cont_msg">
                                        <img src="{{ asset('images/chat-bubble.png') }}" class=" user_img_msg">
                                    </div>
                                    <div class="msg_cotainer images">
                                        <h6>{{ $comment->user->first_name.' '.$comment->user->last_name }}</h6>
{{--                                        <p><strong>Name: </strong>{{ $comment->name }}</p>--}}
                                        <p><strong>Notes: </strong>{{ $comment->notes }}</p>
                                      <?php
                                        $images = json_decode($comment->file);
                                      ?>
                                        @foreach($images as $image)
                                            <img height="100px" width="150px" src="{{ asset('storage/'.$image) }}" style=" object-fit: cover; margin-top: 5px;">
                                        @endforeach
                                        <span class="msg_time"><a href="#">{{ $comment->created_at->diffForHumans() }}</a>
                                            @if(\Illuminate\Support\Facades\Auth::user()->id == $comment->user_id)
                                           | &nbsp; <a href="{{ route('sourcing.delete', ['id' => $comment->id]) }}"><i class="fa fa-trash"></i></a>
                                            @endif
                                        </span>
                                        <div id="image-viewer">
                                            <img class="viewer-modal-content" id="full-image">
                                        </div>
                                    </div>

                                </div>

                            @endforeach

                        </div>
                        <div class="card-footer">
                            <form id="commentForm" method="post" action="{{ route('sourcing.create') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="input-group">
                                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                                    <input type="hidden" name="parent_id" value="{{ $sourcing->id }}">
                                    <input type="hidden" name="status" value="published">
                                    <input id="comment_input" required type="text" name="name" class="form-control type_msg" placeholder="Name here...">
                                    <div class="input-group-append">
                                        <span class="input-group-text send_btn"><button type="submit" id="completed-task" class="fabutton"><i class="fa fa-paper-plane"></i></button></span>
                                    </div>
                                </div><br>
                                <textarea id="comment_input" required name="notes" class="form-control type_msg" placeholder="Notes here..."></textarea><br>
                                @include('core/base::forms.partials.images', ['name' => 'file[]', 'values' => ''])
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                .fabutton {
                    background: none;
                    padding: 0px;
                    border: none;
                }
                .img_wrp {
                    display: inline-block;
                    position: relative;
                }
                .close {
                    margin-left: 5px;
                    top: 0;
                    right: 0;
                }
            </style>

        </div>
    </div>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}"/>
@endsection
