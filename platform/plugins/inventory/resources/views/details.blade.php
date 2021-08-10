@extends('core/base::layouts.master')

@section('content')
    <div class="p-3 bg-white">
        <div class="clearfix"></div>

        <br>
        <hr style="border-top: 2px solid #eee;">
        <h2>Inventory Products</h2>

        <div class="table-responsive">
            <table class="table inventory-add">
                <thead>
                <tr>
                    <th scope="col">Image</th>
                    <th scope="col">SKU</th>
                    {{--<th scope="col">Barcode</th>--}}
                    <th scope="col">Sec</th>
                    <th scope="col">Name</th>
                    <th scope="col">E-commerce Qty</th>
                    <th scope="col">Ordered Qty</th>
                    <th scope="col">Cost Price</th>
                    <th scope="col">Pvt. Label</th>
                    {{--<th scope="col">Sale Price</th>--}}
                    <th scope="col">Received Qty</th>
                    {{--<th scope="col">Single Qty</th>--}}
                </tr>
                </thead>
                <tbody id="tableBody">
                @if(count($inventoryDetail->products))
                    @foreach($inventoryDetail->products as $key => $product)
                        <tr data-id="{{ $product->pid }}">

                            @if(!$product->is_variation)
                                <td class=" text-center column-key-image">
                                    <a href="" title="">
                                        <img src="{{ URL::to('storage') }}/{{@json_decode($product->pimages)[0]}}"
                                             onerror="this.src='{{ asset('images/lucky&blessed_logo_sign_Black 1.png') }}'"
                                             width="50">
                                    </a>
                                </td>
                            @else
                                <td></td>
                            @endif

                            <td>{{ $product->sku }}</td>

                            {{--@if(!$product->is_variation)--}}
                            {{--@if($product->barcode)--}}
                            {{--    <td><img src="{{asset('storage/'.$product->barcode)}}" width="100%" height="30px"><input type="hidden" name="barcode_{{ $loop->iteration-1 }}" value="{{ $product->barcode }}"></td>--}}
                            {{--@else--}}
                            {{--    <td></td>--}}
                            {{--@endif--}}

                            <td>{{ $product->warehouse_sec }}</td>

                            <td>{{ $product->pname }}</td>

                            @if($product->is_variation)
                                <td>{{ $product->pquantity }}</td>
                            @else
                                <td></td>
                            @endif

                            @if(!$product->is_variation)
                                <td>{{ $product->ordered_qty }}</td>
                                <td>{{ $product->price }}</td>
                                {{--<td>{{ $product->sale_price }}</td>--}}
                            @else
                                <td></td>
                                <td></td>
                                {{--<td></td>--}}
                            @endif

                            <td>{{ $product->private_label ? 'Yes' : 'No' }}</td>

                            @if($product->is_variation)
                                <td>{{$product->received_qty}}</td>
                            @else
                                <td></td>
                            @endif

                            {{--<td><input style="width: 60px; text-align:center" name="loose_qty_{{ $loop->iteration-1 }}" id="loose_qty_{{ $product->pid }}" class="input-micro input-both-amount input_main" value="{{ $product->loose_qty }}"></td>--}}

                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>


    </div>
@stop
