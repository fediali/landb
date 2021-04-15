@extends('core/base::layouts.master')
@section('content')
    <div class="p-3 bg-white">
        {!! Form::open(['route' => 'orders.import-order', 'class' => 'ps-form--account-setting', 'method' => 'POST','enctype'=>'multipart/form-data']) !!}

        <div class="row">
            <div class="col-lg-12 mt-2">
                <label for="name">Market Place:</label>
                {!! Form::select('market_place',\Botble\Ecommerce\Models\Order::$MARKETPLACE, null, ['class' => 'form-control','required']) !!}
            </div>
            {!! Form::error('market_place', $errors) !!}

            <div class="col-lg-12 mt-2">
                <label for="name">Market Place:</label>
                {!! Form::file('file',null, ['class' => 'form-control btn_gallery','required']) !!}
            </div>
            {!! Form::error('market_place', $errors) !!}

            <div class="form-group col-lg-3">
                <button class="btn btn-primary btn-lg" type="submit">Upload</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
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
</style>
