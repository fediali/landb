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
                    <p class="m-0 heading">Thread Status</p>
                    <p>{{$orderDetail->thread_status}}</p>
                </div>
                <div class="col-lg-3">
                    <p class="m-0 heading">Vendor</p>
                    <p>{{$orderDetail->vendor->getFullName()}}</p>
                </div>
                @if($orderDetail->thread_status == \Botble\Thread\Models\Thread::PRIVATE && $orderDetail->pvt_customer_id)
                    <div class="col-lg-3">
                        <p class="m-0 heading">Pvt. Customer</p>
                        <p>{{$orderDetail->pvt_customer->name}}</p>
                    </div>
                @endif
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
                <div class="row">
                    <div class="col-lg-12">
                    <ul class="accordion-list-c">
                    @foreach($orderDetail->threadOrderVariations() as $variation)
                    <li>
                        <h3 class="h-a">{{$loop->iteration}}. {{$variation->name}}</h3>
                        <div class="answer">
                        <div class="row">
                        <div class="col-lg-12 mb-3">
                            <img class="w-100 mt-3" src="{{ asset('storage/'.strtolower(@$variation->design_file)) }}" height="120" width="120" style="object-fit: cover">
                        </div>
                        <div class="col-lg-3">
                            <p class="m-0 heading">SKU</p>
                            <p>{{$variation->sku}}</p>
                        </div>
                        <div class="col-lg-2">
                            <p class="m-0 heading">Type</p>
                            <p>{{$variation->category_type}}</p>
                        </div>
                        <div class="col-lg-1">
                            <p class="m-0 heading">{{$orderDetail->is_pieces ? 'Pieces Qty' : 'Pack Qty'}}</p>
                            <p>{{$variation->quantity}}</p>
                        </div>
                        <div class="col-lg-1">
                            <p class="m-0 heading">Cost</p>
                            <p>{{$variation->cost}}</p>
                        </div>
                        <div class="col-lg-2">
                            <p class="m-0 heading">Per Piece Qty</p>
                            <p>{{$variation->per_piece_qty}} {{$variation->unit_name}}</p>
                        </div>
                        <div class="col-lg-2">
                            <p class="m-0 heading">UPC</p>
                            <p>{{$variation->upc}}</p>
                        </div>
                        <div class="col-lg-12">
                            <p class="m-0 heading">Barcode</p>
                            <p><img src="{{asset('storage/'.$variation->barcode)}}" height="30px"></p>
                        </div>
                    </div>

                    @php $product = Botble\Ecommerce\Models\Product::where('sku', $variation->sku)->first(); @endphp
                    <div class="row">
                        @if($product)
                            @foreach($product->variations as $prod_variation)
                                <div class="col-lg-2">
                                    <p class="m-0 heading">Product Variations</p>
                                    <p>
                                        @foreach($prod_variation->productAttributes as $prod_attr)
                                            {{$prod_attr->title}}
                                        @endforeach
                                    </p>
                                </div>
                                <div class="col-lg-2">
                                    <p class="m-0 heading">SKU</p>
                                    <p>{{$prod_variation->product->sku}}</p>
                                </div>
                                <div class="col-lg-2">
                                    <p class="m-0 heading">UPC</p>
                                    <p>{{$prod_variation->product->upc}}</p>
                                </div>
                                <div class="col-lg-6">
                                    <p class="m-0 heading">Barcode</p>
                                    <p><img src="{{asset('storage/'.$prod_variation->product->barcode)}}" height="30px"></p>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    </div>
                    </li>
                    @endforeach
                    </ul>
                    </div>
                </div>
                <br>
                <!-- @foreach($orderDetail->threadOrderVariations() as $variation)
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <h5 class="variation-text">{{$loop->iteration}}. {{$variation->name}} </h5>
                            <img class="w-100" src="{{ asset('storage/'.strtolower(@$variation->design_file)) }}" height="120" width="120" style="object-fit: cover">
                        </div>
                        <div class="col-lg-3">
                            <p class="m-0 heading">SKU</p>
                            <p>{{$variation->sku}}</p>
                        </div>
                        <div class="col-lg-2">
                            <p class="m-0 heading">Type</p>
                            <p>{{$variation->category_type}}</p>
                        </div>
                        <div class="col-lg-1">
                            <p class="m-0 heading">{{$orderDetail->is_pieces ? 'Pieces Qty' : 'Pack Qty'}}</p>
                            <p>{{$variation->quantity}}</p>
                        </div>
                        <div class="col-lg-1">
                            <p class="m-0 heading">Cost</p>
                            <p>{{$variation->cost}}</p>
                        </div>
                        <div class="col-lg-2">
                            <p class="m-0 heading">Per Piece Qty</p>
                            <p>{{$variation->per_piece_qty}} {{$variation->unit_name}}</p>
                        </div>
                        {{--<div class="col-lg-2">
                            <p class="m-0 heading">UPC</p>
                            <p>{{$variation->upc}}</p>
                        </div>
                        <div class="col-lg-12">
                            <p class="m-0 heading">Barcode</p>
                            <p><img src="{{asset('storage/'.$variation->barcode)}}" height="30px"></p>
                        </div>--}}
                    </div>

                    @php $product = Botble\Ecommerce\Models\Product::where('sku', $variation->sku)->first(); @endphp
                    <div class="row">
                        @if($product)
                            @foreach($product->variations as $prod_variation)
                                <div class="col-lg-3">
                                    <p class="m-0 heading">SKU</p>
                                    <p>{{$prod_variation->product->sku}}</p>
                                </div>
                                <div class="col-lg-3">
                                    <p class="m-0 heading">UPC</p>
                                    <p>{{$prod_variation->product->upc}}</p>
                                </div>
                                <div class="col-lg-6">
                                    <p class="m-0 heading">Barcode</p>
                                    <p><img src="{{asset('storage/'.$prod_variation->product->barcode)}}" height="30px"></p>
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endforeach -->
            </div>

        </div>
    </div>
@stop


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
