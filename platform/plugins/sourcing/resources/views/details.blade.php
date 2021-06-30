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

@endsection