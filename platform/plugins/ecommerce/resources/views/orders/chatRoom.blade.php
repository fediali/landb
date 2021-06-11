@extends('core/base::layouts.master')

@section('content')
    <div class="p-3 bg-white" >
        <div class="clearfix"></div>
        <div id="main">

            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-header">Customers</div>
                            <div class="card-body">
                                @if (!count($customers))
                                    <p>No customers</p>
                                @else
                                    <ul class="list-group list-group-flush">
                                        @foreach ($customers as $id => $customer)
                                            <a href="{{ route('orders.messages.chat', [ 'ids' => auth()->user()->id  . '-' . $id ]) }}" class="list-group-item list-group-item-action">{{ $customer }}</a>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-body text-center">
                                <p class="font-weight-bold">You don’t have a chat selected</p>
                                <p>Choose a user to continue an existing chat or start a new one.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop


@section('javascript')
<script>

</script>
@endsection
