<h3>Thread Variations</h3>

@foreach($options['data']->thread_variations as $variation)
    <label>{{$loop->iteration}} : </label>
    <div class="form-group">
        <label for="regular_qty" class="control-label">Regular Qty</label>
        <input class="form-control" name="regular_qty[{{$variation->id}}]" type="number" value="{{$variation->regular_qty}}">
    </div>
    <div class="form-group">
        <label for="plus_qty" class="control-label">Plus Qty</label>
        <input class="form-control" name="plus_qty[{{$variation->id}}]" type="number" value="{{$variation->plus_qty}}">
    </div>
    <div class="form-group">
        <label for="cost" class="control-label">Cost</label>
        <input class="form-control" name="cost[{{$variation->id}}]" type="number" step="0.1" value="{{$variation->cost}}">
    </div>
@endforeach





<script>
  $(document).ready(function () {

  });
</script>


<style>

</style>
