@extends('core/base::layouts.master')

@section('content')
    <div class="p-3 bg-white" >
        <div class="clearfix"></div>
        <div id="main">

            <div class="row">
                <div class="col-lg-12 text-right">
                    <a href="{{url('/admin/threads/details', $orderDetail->thread_id)}}" target="_blank" class="btn btn-icon btn-sm btn-red pl-4 pr-4">View Tech Pack</a>
                    @if($orderDetail->status == 'completed')
                        @if($orderDetail->thread_order_has_pushed)
                            <a href="javascript:void(0)" class="btn btn-sm btn-warning" disabled>Pushed</a>
                        @else
                            <a href="javascript:void(0)" class="pushToEcommerce btn btn-sm btn-info">Push</a>
                        @endif
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 mb-3">
                    <h5 class="order-detail">ORDER DETAIL </h5>
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
                    <p class="m-0 heading"> Regular Pack Category</p>
                    <p>{{@$orderDetail->threadOrderVariations(\Botble\Thread\Models\Thread::REGULAR)[0]->cat_name}}</p>
                </div>
                @if(isset($orderDetail->threadOrderVariations(\Botble\Thread\Models\Thread::PLUS)[0]->cat_name))
                    <div class="col-lg-3">
                        <p class="m-0 heading"> Plus Pack Category</p>
                        <p>{{@$orderDetail->threadOrderVariations(\Botble\Thread\Models\Thread::PLUS)[0]->cat_name}}</p>
                    </div>
                @endif
                <div class="col-lg-3">
                    <p class="m-0 heading">Description</p>
                    <p>{{$orderDetail->name}}</p>
                </div>
                <div class="col-lg-3">
                    <p class="m-0 heading"> PP Sample</p>
                    <p>{{$orderDetail->pp_sample}}</p>
                </div>
                <div class="col-lg-3">
                    <p class="m-0 heading">PP Sample Date</p>
                    @if($orderDetail->pp_sample == \Botble\Thread\Models\Thread::YES)
                        <p>{{date('d F, Y', strtotime($orderDetail->pp_sample_date))}}</p>
                    @else
                        <p>N/A</p>
                    @endif
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
                @if($orderDetail->vendor_product)
                    <div class="col-lg-3">
                        <p class="m-0 heading">Vendor Product</p>
                        <p>{{$orderDetail->vendor_product->name}}</p>
                    </div>
                @endif
            </div>

            <div class="p-3 mb-3 thread-area">
                <div class="row">
                    <div class="col-lg-12 ">
                        <h6 class="mb-1 thread-head"> THREAD VARIATIONS </h6>
                    </div>
                </div>
                <br>
                @foreach($orderDetail->threadOrderVariations() as $variation)
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <h5 class="variation-text">{{$loop->iteration}}. {{$variation->name}} </h5>
                            <img class="w-100" src="{{ asset('storage/'.$variation->design_file) }}" height="120" width="120" style="object-fit: cover">
                        </div>
                        <div class="col-lg-2">
                            <p class="m-0 heading">SKU</p>
                            <p>{{$variation->sku}}</p>
                        </div>
                        <div class="col-lg-2">
                            <p class="m-0 heading">Type</p>
                            <p>{{$variation->category_type}}</p>
                        </div>
                        <div class="col-lg-2">
                            <p class="m-0 heading">Qty</p>
                            <p>{{$variation->quantity}}</p>
                        </div>
                        <div class="col-lg-2">
                            <p class="m-0 heading">Cost</p>
                            <p>{{$variation->cost}}</p>
                        </div>
                        <div class="col-lg-2">
                            <p class="m-0 heading">Product Unit</p>
                            <p>{{$variation->unit_name}}</p>
                        </div>
                        <div class="col-lg-2">
                            <p class="m-0 heading">Per Piece Qty</p>
                            <p>{{$variation->per_piece_qty}}</p>
                        </div>
                    </div>
                @endforeach
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
        background-color: #d64635 !important;
        border-color: #d64635 !important;
        color: #fff !important;
    }
    .thread-area {
        background: #f3f3f3;
        border-radius: 10px;
        -moz-box-shadow: 0 0 5px #999;
        -webkit-box-shadow: 0 0 5px #999;
        box-shadow: 0 0 5px #999;
    }
    .thread-head {
        font-size:16px !important;
    }
    .order-detail {
        font-size:20px !important;
    }
</style>

@section('javascript')
<script>

    $('a.pushToEcommerce').on('click', () => {
        let url = "{{route('threadorders.orderItem', $orderDetail->id)}}";
        confirm_start(url);
    });

    function confirm_start(url){
        swal({
            title: 'Are you sure?',
            text: "Do you want to push this Order to Ecommerce!",
            icon: 'info',
            buttons:{
                cancel: {
                    text: "Cancel",
                    value: null,
                    visible: true,
                    className: "",
                    closeModal: true,
                },
                confirm: {
                    text: "Push",
                    value: true,
                    visible: true,
                    className: "",
                    closeModal: true
                }
            }
        }).then((result) => {
            if (result) {
                location.replace(url)
            }
        });
    }
</script>
@endsection
