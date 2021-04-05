
<br><hr style="border-top: 2px solid #eee;"><h2>Products</h2>
<div class="form-group">
    <input class="form-control" id="scannerInput" type="text" placeholder="Scan Barcode to add product to list">
    <span id="product-error" class="invalid-feedback"></span>
</div>
<table class="table inventory-add">
    <thead>
    <tr>
        <th scope="col">Image</th>
        <th scope="col">SKU</th>
        <th scope="col">Barcode</th>
        <th scope="col">Name</th>
        <th scope="col">E-commerce Pack Qty</th>
        <th scope="col">Ordered Pack Qty</th>
        <th scope="col">Cost Price</th>
        <th scope="col">Sale Price</th>
        <th scope="col">Received Pack Qty</th>
        <th scope="col">Single Qty</th>
        <th scope="col">Action</th>
        <th scope="col"></th>
    </tr>
    </thead>
    <tbody id="tableBody">
        @if(count($options['data']->products))
            @foreach($options['data']->products as $key => $product)
            <tr data-id="{{ $product->pid }}">
                <td class=" text-center column-key-image">
                    <a href="" title="">
                        <img src="{{ URL::to('storage') }}/{{@json_decode($product->pimages)[0]}}" onerror="this.src='{{ asset('images/lucky&blessed_logo_sign_Black 1.png') }}'" width="50">
                    </a>
                </td>
                <td>{{ $product->sku }}<input type="hidden" name="sku_{{ $loop->iteration-1 }}" value="{{ $product->sku }}"><input type="hidden" name="product_id_{{ $loop->iteration-1 }}" value="{{ $product->pid }}"></td>
                <td><img src="{{asset('storage/'.$product->barcode)}}" width="100%" height="30px"><input type="hidden" name="barcode_{{ $loop->iteration-1 }}" value="{{ $product->barcode }}"></td>
                <td>{{ $product->pname }}</td>
                <td>{{ $product->pquantity }}<input type="hidden" name="quantity_{{ $loop->iteration-1 }}" value="{{ $product->pquantity }}"></td>
                <td>{{ $product->ordered_pack_qty }}<input type="hidden" name="ordered_qty_{{ $loop->iteration-1 }}" value="{{ $product->ordered_pack_qty }}"></td>
                <td>{{ $product->price }}</td>
                <td>{{ $product->sale_price }}</td>
                <td><input style="width: 60px; text-align:center" name="received_qty_{{ $loop->iteration-1 }}" id="received_qty_{{ $product->pid }}" class="input-micro input-both-amount input_main" value="{{ $product->received_pack_qty }}"></td>
                <td><input style="width: 60px; text-align:center" name="loose_qty_{{ $loop->iteration-1 }}" id="loose_qty_{{ $product->pid }}" class="input-micro input-both-amount input_main" value="{{ $product->loose_qty }}"></td> 
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
<script src="{{ asset('js/barcodeScanner.js') }}"></script>
<script>
  $(document).scannerDetection({
    //https://github.com/kabachello/jQuery-Scanner-Detection
    timeBeforeScanTest: 200, // wait for the next character for upto 200ms
    avgTimeByChar: 40, // it's not a barcode if a character takes longer than 100ms
    preventDefault: false,
    endChar: [13],
    onComplete: function(barcode, qty){
      validScan = true;
      $('#scannerInput').val(barcode);
    } // main callback function	,
    ,
    onError: function(string, qty) {
        console.log('Something went wrong. Try again!');
    }
  });

  $(document).ready(function () {
    var input = document.getElementById("scannerInput");
    // Execute a function when the user releases a key on the keyboard
        input.addEventListener("keyup", function(event) {
          // Number 13 is the "Enter" key on the keyboard
          if (event.keyCode === 13) {
            // Cancel the default action, if needed
            event.preventDefault();
            var $t = $(event.currentTarget);
            $t.addClass('loading');
            $('#product-error').hide();
            $('#scannerInput').removeClass('is-invalid');
            get_product_by_barcode(this.value, $t);
          }
        });
        var form = document.getElementsByTagName("form");
        form[0].addEventListener("keydown", function(event){
          if(event.keyCode === 13){
            event.preventDefault();
          }
        })
  });

  function get_product_by_barcode(barcode, loader){
    $.ajax({
      type: "GET",
      url: "{{ URL::to('/admin/inventories/get/barcode/product') }}",
      data: {barcode : barcode},
      success: function (result) {
        if (result.status == 'success') {
          var exist = false;
          var product = result.product;
          var pcount = 0;
          $('#tableBody tr').each(function(){
            pcount++;
            var id = $(this).data('id');
            if(id == product.id){
              exist = true;
            }
          });
          if(exist){
            var current = $('#received_qty_'+product.id).val();
            $('#received_qty_'+product.id).val(++current);
              var current2 = $('#loose_qty_'+product.id).val();
              $('#loose_qty_'+product.id).val(++current2);
          }else{
            $('#tableBody').append(
                '<tr data-id="'+product.id+'">\n' +
                '        <td class=" text-center column-key-image"><a href="" title="">\n' +
                '         <img src="{{ URL::to('storage') }}/'+ product.images[0] +'" width="50">\n' +
                '         </a>\n' +
                '        </td>\n' +
                '        <td>'+product.sku+'<input type="hidden" name="sku_'+pcount+'" value="'+product.sku+'"><input type="hidden" name="product_id_'+pcount+'" value="'+product.id+'"></td>\n' +
                '        <td><img src="{{asset('storage')}}/'+ product.barcode +'" width="100%" height="30px"><input type="hidden" name="barcode_'+pcount+'" value="'+product.barcode+'"></td>\n' +
                '        <td>'+product.name+'</td>\n' +
                '        <td>'+product.quantity+'<input type="hidden" name="quantity_'+pcount+'" value="'+product.quantity+'"></td>\n' +
                '        <td>'+product.ordered_qty+'<input type="hidden" name="ordered_qty_'+pcount+'" value="'+product.ordered_qty+'"></td>\n' +
                '        <td>'+product.price+'</td>\n' +
                '        <td>'+product.sale_price+'</td>\n' +
                '        <td><input style="width: 60px; text-align:center" name="received_qty_'+pcount+'" id="received_qty_'+product.id+'" class="input-micro input-both-amount input_main" value="0"></td>\n' +
                '        <td><input style="width: 60px; text-align:center" name="loose_qty_'+pcount+'" id="loose_qty_'+product.id+'" class="input-micro input-both-amount input_main" value="0"></td>\n' +
                '        <td>\n' + 
                '                <a class="btn btn-info" data-toggle="collapse" data-target="#demo">Click</a>\n' +   
                '<div id="demo" class="collapse">\n'+
                '<div class="form-group d-flex"> \n'+   
                '<label class="control-label mt-2 mr-1">Name</label>\n'+
                '<input class="form-control" placeholder="Name" name="name" type="text"  > \n'+
                '</div>\n'+
                '</div>\n' +  
                '        </td>\n' +
                '        <td>\n' +
                '            <div class="btn-group">\n' +
                '                <a class="btn dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cog"></i><span class="caret"></span></a>\n' +
                '                <div class="dropdown-menu">\n' +
                '                    <a class="dropdown-item" onclick="deleteProduct('+product.id+')" href="javascript:void(0)">Delete</a>\n' +
                '                </div>\n' +
                '            </div>\n' +
                '        </td>\n' +               
                '</tr>\n' + 
                '<tr>\n' +
                '</tr>' 
            );
          }
          loader.removeClass('loading');
        }
      },
      error: function (result) {
        $('#product-error').html('Product Not found!');
        $('#product-error').show();
        $('#scannerInput').addClass('is-invalid');
        loader.removeClass('loading');
      }
    });
  }

  function deleteProduct(pid){
    $('#tableBody tr').each(function(){
      var id = $(this).data('id');
      if(id == pid){
        $(this).remove();
      }
    });
  }
</script>

<style>
    #scannerInput {
        box-sizing: border-box;
        height: 30px;
        padding: 10px;
    }
    #scannerInput.loading {
        background: url(http://www.xiconeditor.com/image/icons/loading.gif) no-repeat right center;
    }
</style>
