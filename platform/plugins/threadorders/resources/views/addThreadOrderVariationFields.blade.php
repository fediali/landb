<h3>Thread Variations</h3>
@foreach($options['data']->thread_variations as $variation)
    <h5>{{$variation->name}}</h5>
    <img class="w-100" src="{{ asset('storage/'.$variation->printDesign->file) }}" height="120" width="120" style="object-fit: cover">
    <div class="col-md-4">
        <div class="form-group">
            <label for="regular_qty" class="control-label">Regular Qty</label>
            <input class="form-control" name="regular_qty[{{$variation->id}}]" type="number" value="{{$variation->regular_qty}}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="plus_qty" class="control-label">Plus Qty</label>
            <input class="form-control" name="plus_qty[{{$variation->id}}]" type="number" value="{{$variation->plus_qty}}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="cost" class="control-label">Cost</label>
            <input class="form-control" name="cost[{{$variation->id}}]" type="number" step="0.1" value="{{$variation->cost}}">
        </div>
    </div>
@endforeach
