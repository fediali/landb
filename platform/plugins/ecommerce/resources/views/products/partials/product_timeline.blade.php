@extends('core/base::layouts.master')
@section('content')
    <section class="timeline">
        <p>
            Product: <strong>{{ $data[0]->product->name }}</strong><br>
            SKU: <strong>{{ $data[0]->product->sku }}</strong><br>
        </p>
        <br>
        <br>
        <div class="container">
            @foreach($data as $history)
                <div class="timeline-item">
                    <div class="timeline-img"></div>
                    <div class="timeline-content timeline-card js--fadeInRight">
                        <div class="date">{{ date('d/m/Y, h:m A', strtotime($history->created_at)) }}</div>
                        <h5 class="mt-3 ml-3">{{ str_replace('_', ' ', strtoupper($history->reference)) }}</h5>
                        <div class="row">
                            @if(!empty($history->thread_order_id))
                                <div class="col-lg-7"><p>Thread Order #{{ $history->thread_order->order_no }}</p></div>
                                <div class="col-lg-5"><p class="muted"><small>Status: &gt; {{ $history->thread_order->status }}</small></p></div>
                            @elseif(!empty($history->order_id))
                                <div class="col-lg-7"><p>Order #{{ $history->order->id }}</p></div>
                                <div class="col-lg-5"><p class="muted"><small>Status: &gt; {{ $history->order->status }}</small></p></div>
                            @elseif(!empty($history->inventory_id))
                                <div class="col-lg-7"><p>Inventory ID: {{ $history->inventory->id }}</p></div>
                                <div class="col-lg-5"><p class="muted"><small>Status: &gt; {{ $history->inventory->status }}</small></p></div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-lg-7"><p>User</p></div>
                            <div class="col-lg-5"><p>{{ $history->user->first_name. ' ' . $history->last_name }}</p></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-7"><p>Qty</p></div>
                            <div class="col-lg-5"><p>{{ ($history->quantity > 0) ? '+'.$history->quantity : 0 }}</p></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-7"><p>New stock</p></div>
                            <div class="col-lg-5"><p>{{ $history->new_stock ? $history->new_stock : 0 }}</p></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-7"><p>Old stock</p></div>
                            <div class="col-lg-5"><p>{{ $history->old_stock ? $history->old_stock : 0 }}</p></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
