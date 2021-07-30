<br>
<hr style="border-top: 2px solid #eee;"><h2>Products</h2>
<div id="scandit-barcode-picker" style="display: none;"></div>
<div class="form-group">
    <div class="input-group">
        <input class="form-control" id="scannerInput" type="text" placeholder="Scan Barcode to add product to list">
        <div class="input-group-prepend">
            <div class="input-group-text" title="Scan barcode using camera" onclick="$('.radial-progress').click();"><i
                    class="fa fa-barcode"></i></div>
        </div>
    </div>
    <span id="product-error" class="invalid-feedback"></span>
</div>
<div class="table-responsive">
    <table class="table inventory-add">
        <thead>
        <tr>
            <th scope="col">Image</th>
            <th scope="col">SKU</th>
            {{--        <th scope="col">Barcode</th>--}}
            <th scope="col">Sec</th>
            <th scope="col">Name</th>
            <th scope="col">E-commerce Qty</th>
            <th scope="col">Ordered Qty</th>
            <th scope="col">Cost Price</th>
            <th scope="col">Pvt. Label</th>
            {{--<th scope="col">Sale Price</th>--}}
            <th scope="col">Received Qty</th>
            {{--<th scope="col">Single Qty</th>--}}
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody id="tableBody">
        @if(count($options['data']->products))
            @foreach($options['data']->products as $key => $product)
                <tr data-id="{{ $product->pid }}">

                    @if(!$product->is_variation)
                        <td class=" text-center column-key-image">
                            <a href="" title=""><img
                                    src="{{ URL::to('storage') }}/{{@json_decode($product->pimages)[0]}}"
                                    onerror="this.src='{{ asset('images/lucky&blessed_logo_sign_Black 1.png') }}'"
                                    width="50"></a>
                        </td>
                    @else
                        <td></td>
                    @endif

                    <td>{{ $product->sku }}<input type="hidden" name="sku_{{ $loop->iteration-1 }}"
                                                  value="{{ $product->sku }}"><input type="hidden"
                                                                                     name="product_id_{{ $loop->iteration-1 }}"
                                                                                     value="{{ $product->pid }}"></td>

                    {{--@if(!$product->is_variation)--}}
                    {{--                    @if($product->barcode)--}}
                    {{--                        <td><img src="{{asset('storage/'.$product->barcode)}}" width="100%" height="30px"><input type="hidden" name="barcode_{{ $loop->iteration-1 }}" value="{{ $product->barcode }}"></td>--}}
                    {{--                    @else--}}
                    {{--                        <td></td>--}}
                    {{--                    @endif--}}

                    @if($product->warehouse_sec == null ||$product->warehouse_sec->isEmpty())
                        <td>sss</td>
                    @else
                        <td>{{ $product->warehouse_sec }}</td>
                    @endif()

                    <td>{{ $product->pname }}</td>

                    @if($product->is_variation)
                        <td>{{ $product->pquantity }}<input type="hidden" name="quantity_{{ $loop->iteration-1 }}"
                                                            value="{{ $product->pquantity }}"></td>
                    @else
                        <td></td>
                    @endif

                    @if(!$product->is_variation)
                        <td>{{ $product->ordered_qty }}<input type="hidden" name="ordered_qty_{{ $loop->iteration-1 }}"
                                                              value="{{ $product->ordered_qty }}"></td>
                        <td>{{ $product->price }}</td>
                        {{--<td>{{ $product->sale_price }}</td>--}}
                    @else
                        <td></td>
                        <td></td>
                        {{--<td></td>--}}
                    @endif

                    <td>{{ $product->private_label ? 'Yes' : 'No' }}</td>

                    @if($product->is_variation)
                        <td><input style="width: 60px; text-align:center" name="received_qty_{{ $loop->iteration-1 }}"
                                   id="received_qty_{{ $product->pid }}"
                                   class="input-micro input-both-amount input_main"
                                   value="{{ $product->received_qty }}"></td>
                    @else
                        <td></td>
                    @endif
                    {{--<td><input style="width: 60px; text-align:center" name="loose_qty_{{ $loop->iteration-1 }}" id="loose_qty_{{ $product->pid }}" class="input-micro input-both-amount input_main" value="{{ $product->loose_qty }}"></td>--}}
                    <td>
                        <div class="btn-group">
                            @if(!$product->is_variation)
                                @if($product->is_released)
                                    <a href="javascript:void(0)" class="btn btn-sm btn-warning" disabled>Released</a>
                                @else
                                    <a href="javascript:void(0)"
                                       onclick='confirm_start("{{route('inventory.releaseProduct', [$options['data']->id, $product->pid])}}")'
                                       class="btn btn-sm btn-info">Release</a>
                                @endif
                            @endif
                            <a href="javascript:void(0)" onclick="deleteProduct({{ $product->pid }})"
                               class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/scandit-sdk@5.x"></script>
<script>

    ScanditSDK.configure("{{ env('SCANDIT_BARCODE_KEY') }}", {
        engineLocation: "https://cdn.jsdelivr.net/npm/scandit-sdk@5.x/build/",
    })
        .then(() => {
            return ScanditSDK.BarcodePicker.create(document.getElementById("scandit-barcode-picker"), {
                // enable some common symbologies
                scanSettings: new ScanditSDK.ScanSettings({
                    enabledSymbologies: ["code39", "code128", "ean8", "ean13", "upca", "upce", "code93"]
                }),
                playSoundOnScan: true,
                vibrateOnScan: true,
                accessCamera: true,
                camera: {
                    cameraType: 'back'
                },
                enablePinchToZoom: true,
                enableTapToFocus: true
            });
        })
        .then((barcodePicker) => {
            // barcodePicker is ready here, show a message every time a barcode is scanned
            barcodePicker.on("scan", (scanResult) => {
                console.log(scanResult.barcodes[0].data)
                $('#scannerInput').val(scanResult.barcodes[0].data);
                $('#scannerInput').addClass('loading');
                $('#product-error').hide();
                $('#scannerInput').removeClass('is-invalid');
                get_product_by_barcode(scanResult.barcodes[0].data, $('#scannerInput'));
            });
        });

    $(document).scannerDetection({
        //https://github.com/kabachello/jQuery-Scanner-Detection
        timeBeforeScanTest: 200, // wait for the next character for upto 200ms
        avgTimeByChar: 40, // it's not a barcode if a character takes longer than 100ms
        preventDefault: false,
        endChar: [13],
        onComplete: function (barcode, qty) {
            validScan = true;
            $('#scannerInput').val(barcode);
        } // main callback function	,
        ,
        onError: function (string, qty) {
            console.log('Something went wrong. Try again!');
        }
    });

    $(document).ready(function () {
        var input = document.getElementById("scannerInput");
        // Execute a function when the user releases a key on the keyboard
        input.addEventListener("keyup", function (event) {
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
        form[0].addEventListener("keydown", function (event) {
            if (event.keyCode === 13) {
                event.preventDefault();
            }
        })
    });

    function get_product_by_barcode(barcode, loader) {
        $.ajax({
            type: "GET",
            url: "{{ URL::to('/admin/inventories/get/barcode/product') }}",
            data: {barcode: barcode},
            success: function (result) {
                if (result.status == 'success') {

                    for (let i = 0; i < result.products.length; i++) {
                        var exist = false;
                        var product = result.products[i];
                        console.log(product, 'product');

                        var pcount = 0;
                        $('#tableBody tr').each(function () {
                            pcount++;
                            var id = $(this).data('id');
                            if (id == product.id) {
                                exist = true;
                            }
                        });
                        if (exist) {
                            var current = $('#received_qty_' + product.id).val();
                            $('#received_qty_' + product.id).val(++current);
                            var current2 = $('#loose_qty_' + product.id).val();
                            $('#loose_qty_' + product.id).val(++current2);
                        } else {

                            let m_img = '<a href="#"><img src="{{ asset('storage') }}/' + product.images[0] + '" width="50"></a>';
                            let b_img = '<img src="{{asset('storage')}}/' + product.barcode + '" width="100%" height="30px"><input type="hidden" name="barcode_' + pcount + '" value="' + product.barcode + '">';

                            let ecom_qty = '<input type="hidden" name="quantity_' + pcount + '" value="' + product.quantity + '">';
                            let ord_qty = '<input type="hidden" name="ordered_qty_' + pcount + '" value="' + product.ordered_qty + '">';
                            let rec_qty = '<input style="width: 60px; text-align:center" name="received_qty_' + pcount + '" id="received_qty_' + product.id + '" class="input-micro input-both-amount input_main" value="0">';
                            //let los_qty = '<input style="width: 60px; text-align:center" name="loose_qty_'+pcount+'" id="loose_qty_'+product.id+'" class="input-micro input-both-amount input_main" value="0">';

                            let dlt_btn = '<a class="dropdown-item" onclick="deleteProduct(' + product.id + ')" href="javascript:void(0)"><i class="fa fa-trash" title="Delete"></i></a>';
                            let warehouse = '<td></td>\n';
                            if (product.is_variation) {
                                m_img = '';
                                // b_img = '';
                                product.name = '';
                                //ecom_qty = '';product.quantity = '';
                                ord_qty = '';
                                product.ordered_qty = '';
                                product.price = '';
                                product.sale_price = '';
                                warehouse = '<td></td>\n';
                            }

                            if (!product.is_variation) {
                                rec_qty = '';
                                ecom_qty = '';
                                product.quantity = '';
                                //dlt_btn = '';

                                if (product.warehouse_sec !=="") {
                                    warehouse = ' <td>' + product.warehouse_sec + '</td>\n'
                                } else {
                                    warehouse = '<td><input type="text" name="warehouse_sec"></td>\n'
                                }

                                // warehouse = '<td>'+product.warehouse_sec+'</td>\n';
                            }

                            let private_label = 'No';
                            if (product.private_label) {
                                private_label = 'Yes';
                            }

                            $('#tableBody').append(
                                '<tr data-id="' + product.id + '">\n' +
                                '        <td class=" text-center column-key-image">' + m_img + '</td>\n' +
                                '        <td>' + product.sku + '<input type="hidden" name="sku_' + pcount + '" value="' + product.sku + '"><input type="hidden" name="product_id_' + pcount + '" value="' + product.id + '"></td>\n' +
                                // '        <td>'+b_img+'</td>\n' +
                                warehouse +
                                '        <td>' + product.name + '</td>\n' +
                                '        <td>' + product.quantity + ' ' + ecom_qty + '</td>\n' +
                                '        <td>' + product.ordered_qty + ' ' + ord_qty + '</td>\n' +
                                '        <td>' + product.price + '</td>\n' +
                                '        <td>' + private_label + '</td>\n' +
                                '        <td>' + rec_qty + '</td>\n' +
                                '        <td>' + dlt_btn + '</td>\n' +
                                '</tr>'
                            );
                        }
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

    function deleteProduct(pid) {
        $('#tableBody tr').each(function () {
            var id = $(this).data('id');
            if (id == pid) {
                $(this).remove();
            }
        });
    }

    function confirm_start(url) {
        swal({
            title: 'Are you sure?',
            text: "Do you want to release this Product to E-commerce!",
            icon: 'info',
            buttons: {
                cancel: {
                    text: "Cancel",
                    value: null,
                    visible: true,
                    className: "",
                    closeModal: true,
                },
                confirm: {
                    text: "Push",
                    value: true,
                    visible: true,
                    className: "",
                    closeModal: true
                }
            }
        }).then((result) => {
            if (result) {
                location.replace(url)
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
