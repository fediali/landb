<span data-toggle="modal" data-target="#prodVarModal-{{$item->id}}" title="{{$skuQty}}" style="cursor:pointer">{{$singleQty}}</span>


<div class="modal fade in" id="prodVarModal-{{$item->id}}" style="display: none; padding-right: 17px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex w-100">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">X</button>
                    <h4 class="modal-title text-center w-100 thread-pop-head">
                        Product Variation Qty
                    </h4>
                </div>
            </div>
            <form method="post" action="{{route('products.update.prod.var.qty')}}">
                @csrf
                <div class="modal-body">
                    <?php
                    use Botble\Ecommerce\Models\ProductVariation;
                    $getSingleProds = ProductVariation::where('configurable_product_id', $item->id)->where('is_default', 0)->get();
                    ?>
                    @foreach($getSingleProds as $singleProd)
                        <div class="mt-3">
                            <label class="font-bold">SKU {{$singleProd->product->sku}}:</label>
                            <input type="number" name="product_qty[{{$singleProd->product_id}}]" value="{{$singleProd->product->quantity}}" class="form-control" required>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary" value="Submit">
                </div>
            </form>
        </div>
    </div>
</div>
