@extends('core/base::layouts.master')

@section('content')
    <div class="p-3 bg-white">
        {!! Form::open(['route' => 'orders.import-order', 'class' => 'ps-form--account-setting', 'method' => 'POST','enctype'=>'multipart/form-data']) !!}
        <div class="row">
            <div class="col-lg-4"></div>
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-lg-12 mt-2">
                        <label for="name">Market Place:</label>
                        {!! Form::select('market_place',\Botble\Ecommerce\Models\Order::$MARKETPLACE, null, ['class' => 'form-control selectpicker select-order','data-live-search'=>'true','required' ]) !!}
                    </div>
                    {!! Form::error('market_place', $errors) !!}
                    <div class="col-lg-12 mt-2">
                        <label for="name">Market Place:</label><br>
                        {!! Form::file('file',null, ['class' => 'form-control btn_gallery','required']) !!}
                    </div>
                    {!! Form::error('market_place', $errors) !!}
                    <div class="form-group col-lg-12 mt-3">
                        <button class="btn btn-primary btn-lg w-100" type="submit">Upload</button>
                    </div>
                </div>
            </div>
            <div class="col-lg-4"></div>
        </div>
        {!! Form::close() !!}
    </div>
    <br>
    @if($import_errors && count($import_errors))
        <div class="col-md-12">
            <div class="row">
                <ul>
                    @foreach($import_errors as $error)
                        <li style="color: red">{{$loop->iteration}}. {{$error}}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if($import != null)
        <div class="p-3 bg-white mt-4">
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table class="table table-striped w-100">
                        <thead>
                        <tr>
                            <th><input type="checkbox"/></th>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Order Date</th>
                            <th>From</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($import as $order)
                            <tr>
                                <td><input type="checkbox"/></td>
                                <td>
                                    <p class="m-0">
                                        <a target="_blank" href="{{route('orders.edit',[$order->id])}}">{{$order->id}}</a>
                                    </p>
                                    {{--<p style="font-size:12px" class="m-0">--}}
                                    {{--Street, Rochester, IN, 40975, US--}}
                                    {{--</p>--}}
                                </td>
                                <td>
                                    <p class="m-0">{{$order->user->name}} </p>
                                </td>
                                <td>{{$order->amount}}</td>
                                <td>{{$order->import->order_date}}</td>
                                <td>
                                    @if($order->import->type == \Botble\Ecommerce\Models\Order::LASHOWROOM)
                                        LA Showroom
                                    @elseif($order->import->type == \Botble\Ecommerce\Models\Order::FASHIONGO)
                                        Fashion Go
                                    @else
                                        Orange Shine
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@stop

<style>
    .heading {
        color: #d64635;
        font-weight: 600;
    }

    .order-detail {
        font-size: 20px !important;
    }

    .img-circle {
        border-radius: 10px;
        width: 100%;
    }

    .table td {
        padding: 10px 5px !important;
        font-size: 14px;
    }

    .select-order button {
        height: 40px;
    }
</style>

<script>
    $('select').selectpicker();
</script>
