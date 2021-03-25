@extends('core/base::layouts.master')

@section('content')
    <div class="page-content " style="min-height: 1111px;">

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Threads</li>
            <li class="breadcrumb-item active">View Order</li>
        </ol>

        <div class="clearfix"></div>

        <div id="main">
            <div class="row">
                <div class="col-lg-12 mb-3">
                    <h5>ORDER DETAIL </h5>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <p class="m-0 heading">Vendor</p>
                    <p>{{$orderDetail->vendor->getFullName()}}</p>
                </div>
                <div class="col-lg-3">
                    <p class="m-0 heading">Order No.</p>
                    <p>{{$orderDetail->order_no}}</p>
                </div>
                <div class="col-lg-3">
                    <p class="m-0 heading"> Regular Category</p>
                    <p>{{$orderDetail->thread->regular_product_categories[0]->name}}</p>
                </div>
                @if(isset($orderDetail->thread->plus_product_categories[0]->name))
                    <div class="col-lg-3">
                        <p class="m-0 heading"> Plus Category</p>
                        <p>{{@$orderDetail->thread->plus_product_categories[0]->name}}</p>
                    </div>
                @endif
                <div class="col-lg-3">
                    <p class="m-0 heading">Description</p>
                    <p>{{$orderDetail->name}}</p>
                </div>
                <div class="col-lg-3">
                    <p class="m-0 heading">PP Sample Date</p>
                    <p>{{date('d F, Y', strtotime($orderDetail->pp_sample_date))}}</p>
                </div>
                <div class="col-lg-3">
                    <p class="m-0 heading"> PP Sample</p>
                    <p>{{$orderDetail->pp_sample}}</p>
                </div>
                @if($orderDetail->material)
                    <div class="col-lg-3">
                        <p class="m-0 heading">Fabric</p>
                        <p>{{$orderDetail->material}}</p>
                    </div>
                @endif
                <div class="col-lg-3">
                    <p class="m-0 heading">Select Shipping Method</p>
                    <p>{{$orderDetail->shipping_method}}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 mb-3">
                    <h5 class="mb-0"> THREAD VARIATIONS </h5>
                </div>
            </div>

            @foreach($orderDetail->thread_order_variations as $variation)
                <div class="row">
                    <h5 class="variation-text"> {{$variation->name}} </h5>
                    <div class="col-lg-3">
                        <p class="m-0 heading">Regular Qty</p>
                        <p>{{$variation->name}}</p>
                    </div>
                    <div class="col-lg-3">
                        <p class="m-0 heading">Plus Qty</p>
                        <p>{{$variation->name}}</p>
                    </div>
                    <div class="col-lg-3">
                        <p class="m-0 heading">Cost</p>
                        <p>{{$variation->cost}}</p>
                    </div>
                </div>
            @endforeach

            <div class="row">
                <div class="col-lg-12 text-right">
                    <a href="{{url('/admin/threads/details', $orderDetail->thread_id)}}" class="btn btn-icon btn-sm btn-red pl-4 pr-4">Tech Pack</a>
                </div>
            </div>
        </div>

    </div>
@stop

<style>
    .heading{
        color: #d64635;
        font-weight: 600;
    }
    .variation-text {
        color: #696969;
    }
    .btn-red {
        background-color: #d64635;
        border-color: #d64635;
        color: #fff;
    }
</style>
