@extends('core/base::layouts.master')
@section('content')
    <p>
        Product: <strong><a href="{{ route('products.edit', ['product' => $data->id]) }}">{{ $data->name }}</a></strong><br>
        SKU: <strong>{{ $data->sku }}</strong><br>
    </p>
    <table width="100%" class="table table-middle">
        <thead>
        <tr>
            <th width="10%" class="center">Qty</th>
            <th width="10%" class="center">New stock</th>
            <th width="10%" class="center">Old stock</th>
            <th><span>Options</span></th>
            <th>User</th>
            <th>Detailed information</th>
            <th>_reference</th>
            <th width="10%">Date</th>
        </tr>
        </thead>
        <tbody>
    @foreach($data->inventory_history as $history)
        <tr class="cm-row-status-a">
            <td class="center"><strong>{{ ($history->quantity > 0) ? '+'.$history->quantity : $history->quantity }}</strong></td>
            <td class="center">{{ $history->new_stock }}</td>
            <td class="center">{{ $history->old_stock }}</td>
            <td class="nowrap"><p class="muted"><small></small></p></td>
            <td class="nowrap">{{ $history->user->first_name. ' ' . $history->last_name }}</td>
            <td class="nowrap">
                @if(!empty($history->order_id))
                    <a href="{{ route('threadorders.threadOrderDetail', ['id' => $history->order->id]) }}" target="_blank">Order #{{ $history->order->order_no }}</a>
                    <p class="muted"><small>Status: ThreadOrder &gt; {{ $history->order->status }}</small></p>
                @elseif(!empty($history->inventory_id))
                    <a href="{{ route('inventory.edit', ['inventory' => $history->inventory->id]) }}" target="_blank">Inventory ID: {{ $history->inventory->id }}</a>
                    <p class="muted"><small>Status: Inventory &gt; {{ $history->inventory->status }}</small></p>
                @endif

            </td>
            <td class="nowrap"><p class="muted">{{ $history->reference }}</p></td>
            <td class="nowrap">{{ date('d/m/Y, h:m A', strtotime($history->created_at)) }}</td>
        </tr>
    @endforeach
        </tbody></table>

@endsection