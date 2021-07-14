@foreach($threads as $thread)

    <?php
    $selectedRegCat = $selectedPluCat = $reg_cat = $plus_cat = null;
    $reg_sku = $plus_sku = '';

    $selectedRegCat = $thread->regular_product_categories()->first(['product_category_id', 'sku']);
    $selectedPluCat = $thread->plus_product_categories()->first(['product_category_id', 'sku']);

    if (!empty($selectedRegCat->product_category_id)) {
        $reg_cat = Botble\Ecommerce\Models\ProductCategory::with('category_sizes')->find($selectedRegCat->product_category_id);
    }
    if (!empty($selectedPluCat->product_category_id)) {
        $plus_cat = Botble\Ecommerce\Models\ProductCategory::with('category_sizes')->find($selectedPluCat->product_category_id);
    }

    $reg_sku = @$selectedRegCat->sku;
    $plus_sku = @$selectedPluCat->sku;

    $variations = get_thread_variations($thread->id);
    ?>

    @if ($thread->is_denim == 1)
        <div id="DivDenimToPrint">

            <table style="border: 1px solid #333;border-collapse: collapse; border-spacing: 0; width: 100%; margin-top: 15px;">
                <tbody>
                <tr>
                    <td style="border: 1px solid #333;text-align: center;" colspan="1" rowspan="3"
                        class="tablelogo">
                        <img src="http://localhost/landb/public/images/lucky&amp;blessed_logo_sign_Black 1.png" alt="">
                    </td>
                    <td style="width: 12%; border: 1px solid #333; padding:10px;" colspan="1" rowspan="1">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif; margin: 0px;">Order Date:<br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; ">{{ parse_date($thread->order_date) }}</span>
                        </p>
                    </td>
                    <td style="border: 1px solid #333;  padding:0px 10px;" rowspan="1" colspan="1">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">Description <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; ">{{ $thread->name }}</span>
                        </p>
                    </td>
                    <td style="border: 1px solid #333;  padding:0px 10px;" colspan="1">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">Designer: <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; "> {{ $thread->designer->first_name.' '.$thread->designer->last_name }}</span>
                        </p>
                    </td>
                    <td style="width: 8%; border: 1px solid #333; vertical-align: top;" colspan="1" rowspan="2" class="p-0">
                        <div class="regpack">
                            <h6 style="font-size: 12px; margin:0px; background: #333;  font-family: 'Raleway', sans-serif; color: #fff; padding: 4px; text-align: center; text-transform: uppercase; font-weight: 400;">{{$thread->thread_status == \Botble\Thread\Models\Thread::PRIVATE ? 'Sizes' : 'Reg Pack Size Run'}}</h6>
                            @if($reg_cat)
                                @foreach($reg_cat->category_sizes as $key => $reg_catVal)
                                    <div class="sizediv">
                                        {{ $thread->thread_status == \Botble\Thread\Models\Thread::PRIVATE ? strtok($reg_catVal->name,'-') : $reg_catVal->name }}
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </td>

                    @if(!empty($plus_cat))
                        <td style="width: 8%;border: 1px solid #333; vertical-align: top;" colspan="1" rowspan="2" class="p-0">
                            <div class="regpack">
                                <h6 style="font-size: 12px; margin:0px; background: #333;  font-family: 'Raleway', sans-serif; color: #fff; padding: 4px; text-align: center; text-transform: uppercase; font-weight: 400;">Plus Pack Size</h6>
                                @if($plus_cat)
                                    @foreach($plus_cat->category_sizes as $key => $plus_catVal)
                                        <div class="sizediv">
                                            {{ $thread->thread_status == \Botble\Thread\Models\Thread::PRIVATE ? strtok($plus_catVal->name,'-') : $plus_catVal->full_name }}
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </td>
                    @endif

                    <td style="width: 13%;border: 1px solid #333;  padding:0px 10px;" rowspan="1" colspan="2">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">PP Sample Due Date <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; "> {{ parse_date($thread->pp_sample_date) }}</span>
                        </p>
                    </td>
                    <td style="border: 1px solid #333;  padding:0px 10px;" colspan="2">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">Request PP Sample: <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; ">{{ @$thread->pp_sample }}</span>
                        </p>
                    </td>
                    <td style="border: 1px solid #333;  padding:0px 10px;">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">PP Sample Size: <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; "> {{ @$thread->pp_sample_size }} </span>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="width: 14%;border: 1px solid #333;  padding:0px 10px;">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">
                            Style # <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; ">
                                Reg Pack:  {{ $reg_sku }} <br>
                                @if(!empty($plus_sku))
                                    Plus Pack:  {{ $plus_sku }}
                                @endif
                            </span>
                        </p>
                    </td>
                    <td colspan="2" style="width: 14%;border: 1px solid #333;  padding:0px 10px;">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">Category <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; ">
                                Reg Pack: {{ @$reg_cat->name }}<br>
                                <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; ">
                                    @if(!empty($plus_cat))
                                        Plus Pack: {{ $plus_cat->name }}
                                    @endif
                                </span>
                            </span>
                        </p>
                    </td>
                    <td style="border: 1px solid #333;  padding:0px 10px;" colspan="2">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">
                            Season: <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; ">  {{ @$thread->season->name }}</span>
                        </p>
                    </td>
                    <td style="border: 1px solid #333;  padding:0px 10px;" colspan="2">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">
                            Vendor: <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; ">{{ @$thread->vendor->first_name.' '.@$thread->vendor->last_name }}</span>
                        </p>
                    </td>
                    <td style="border: 1px solid #333;  padding:0px 10px;" colspan="1">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">
                            Status: <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; ">{{ $thread->thread_status }}</span>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid #333;  padding:0px 10px;" colspan="3">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">
                            Shipping Method: <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; "> {{ $thread->shipping_method }}</span>
                        </p>
                    </td>
                    <td style="border: 1px solid #333;  padding:0px 10px;" colspan="3">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">
                            Ship Date: <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; "> {{ parse_date($thread->ship_date) }}</span>
                        </p>
                    </td>
                    <td style="border: 1px solid #333;  padding:0px 10px;" colspan="4">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">
                            No Later Than <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; "> {{ parse_date($thread->cancel_date) }}</span>
                        </p>
                    </td>
                </tr>
                </tbody>
            </table>

            <div style="margin: 30px 0;border: solid 1px #333;padding: 15px;border-radius: 5px;">
                <div style=" display: flex;margin-right: -15px;margin-left: -15px;">
                    <div style=" flex: 0 0 20.333333%;max-width: 20.333333%; position: relative;width: 100%;padding-right: 15px;padding-left: 15px;">
                        <h4 style=" text-align: center;font-size: 16px;text-transform: uppercase;margin: 0; font-family: 'Raleway', sans-serif;">Style</h4>
                        @if(!is_null($thread->spec_files))
                            @if(count($thread->spec_files))
                                <div style=" max-width: 1000px;
                      position: relative;
                      margin: auto; margin-top: 1.5rem !important;">
                                    @foreach($thread->spec_files as $file)
                                        <div>
                                            <img src="{{ asset($file->spec_file) }}" style="width:100%; height:310px;">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                        <br>
                    </div>
                    <div style="flex: 0 0 74.76667%;max-width: 74.76667%; position: relative;width: 100%;padding-right: 15px;padding-left: 15px;">
                        <div>
                            <h4 style="text-align: center;font-size: 16px;text-transform: uppercase;margin: 0; font-family: 'Raleway', sans-serif;">SPECIFICATIONS</h4>
                            <div class="denim_table">
                                <table style="border: 1px solid #333;border-collapse: collapse;height: 100%; width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style=" font-weight: 600; font-family: 'Raleway', sans-serif;padding: 8px; vertical-align: top;  font-size: 14px;" colspan="1" rowspan="1">Inseam: {{ $thread->inseam }}</th>
                                        <th style=" font-weight: 600; font-family: 'Raleway', sans-serif;padding: 8px; vertical-align: top;  font-size: 14px;" colspan="1" rowspan="1">Label: {{ @$thread->label }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td style="background: #ff442e; font-weight: 600; font-family: 'Raleway', sans-serif; border: 1px solid #333; padding: 8px; vertical-align: top;  font-size: 14px;" colspan="12">
                                            <div style=" font-weight: 600; font-family: 'Raleway', sans-serif;display: flex; justify-content: space-between;">
                                                <b>Fit</b>
                                                @foreach(array_chunk($fits, 5, true) as $fits)
                                                    <div class="item">
                                                        @foreach($fits as $key => $fit)
                                                            <div style=" font-weight: 600; font-family: 'Raleway', sans-serif;display: flex;  justify-content: space-between; align-items: baseline;">
                                                                <label for=""> {{ $fit }}</label>
                                                                <input style=" font-weight: 600; font-family: 'Raleway', sans-serif;background-color: #ffffff; background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFAQMAAAC3obSmAAAABlBMVEUAAADw8PC5otm+AAAAAXRSTlMAQObYZgAAABJJREFUCNdj4GAQYFBgcGBoAAACogD5g5VHSAAAAABJRU5ErkJggg==); border-color: #ff0000;   color: #000000;
        cursor: default;  opacity: 1.65 !important;" type="checkbox" type="checkbox" disabled {!! ($key == $thread->fit_id) ? 'checked' : '' !!}>
                                                                <img src="{{asset('images/checked.png')}}" alt="">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="background: #ff442e; font-weight: 600; font-family: 'Raleway', sans-serif; border: 1px solid #333; padding: 8px; vertical-align: top;  font-size: 14px;" colspan="12">
                                            <div style=" font-weight: 600; font-family: 'Raleway', sans-serif;display: flex; justify-content: space-between;">
                                                <b>Rise</b>
                                                @foreach(array_chunk($rises, 1, true) as $rises)
                                                    <div class="item">
                                                        @foreach($rises as $key => $rise)
                                                            <div style=" font-weight: 600; font-family: 'Raleway', sans-serif;display: flex;  justify-content: space-between; align-items: baseline;">
                                                                <label for=""> {{ $rise }}</label>
                                                                <input style=" font-weight: 600; font-family: 'Raleway', sans-serif;background-color: #ffffff; background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFAQMAAAC3obSmAAAABlBMVEUAAADw8PC5otm+AAAAAXRSTlMAQObYZgAAABJJREFUCNdj4GAQYFBgcGBoAAACogD5g5VHSAAAAABJRU5ErkJggg==); border-color: #ff0000;   color: #000000;
        cursor: default;  opacity: 1.65 !important;" type="checkbox" disabled {!! ($key == $thread->rise_id) ? 'checked' : '' !!}>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style=" font-weight: 600; font-family: 'Raleway', sans-serif; border: 1px solid #333; padding: 8px; vertical-align: top;  font-size: 14px;" colspan="12">
                                            <div style=" font-weight: 600; font-family: 'Raleway', sans-serif;display: flex; justify-content: space-between;">
                                                <b>Reg Pack Qty: </b>{{ $thread->reg_pack_qty }}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style=" font-weight: 600; font-family: 'Raleway', sans-serif; border: 1px solid #333; padding: 8px; vertical-align: top;  font-size: 14px;" colspan="12">
                                            <div style=" font-weight: 600; font-family: 'Raleway', sans-serif;display: flex; justify-content: space-between;">
                                                <b>Plus Pack Qty: </b>{{ $thread->plus_pack_qty }}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style=" font-weight: 600; font-family: 'Raleway', sans-serif; border: 1px solid #333; padding: 8px; vertical-align: top;  font-size: 14px;" colspan="12">
                                            <div style=" font-weight: 600; font-family: 'Raleway', sans-serif;display: flex; justify-content: space-between;">
                                                <b>Fabric Print Direction: </b>
                                                {{ $thread->fabric_print_direction }}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style=" font-weight: 600; font-family: 'Raleway', sans-serif; border: 1px solid #333; padding: 8px; vertical-align: top;  font-size: 14px;" colspan="12">
                                            <div style=" font-weight: 600; font-family: 'Raleway', sans-serif; display: flex !important;">
                                                <div style="display: flex;flex-wrap: wrap;margin-right: -15px;margin-left: -15px;">
                                                    <div style=" font-weight: 600; font-family: 'Raleway', sans-serif; flex: 0 0 50%;max-width: 50%; position: relative;width: 100%;padding-right: 15px;padding-left: 15px;">
                                                        <div style="background: #ffffff;border: 1px solid #d0d0d0;border-radius: 10px; margin-bottom: 1rem !important; padding-right: 1rem !important; padding-left: 1rem !important; font-weight: 600; font-family: 'Raleway', sans-serif;">
                                                            @foreach($variations as $variation)
                                                                @if($variation->status == 'active' && $variation->is_denim == 1)
                                                                    {{--<h5 class=" mt-2">{{$loop->iteration}}. Variation: {{$variation->name}}</h5>--}}
                                                                    <div style=" font-weight: 600; font-family: 'Raleway', sans-serif; display: flex;flex-wrap: wrap;margin-right: -15px;margin-left: -15px;">
                                                                        <div style=" font-weight: 600; font-family: 'Raleway', sans-serif;flex: 0 0 40%;max-width: 40%;position: relative;width: 100%;padding-right: 15px;padding-left: 15px;">
                                                                            <p style=" font-weight: 600; font-family: 'Raleway', sans-serif; margin-bottom: 0 !important; margin-top: 0.5rem !important;">
                                                                                <label for="">Print/Color:</label>
                                                                                {{ @$variation->printdesign->name }}
                                                                            </p>
                                                                            <img height="120" style=" font-weight: 600; font-family: 'Raleway', sans-serif;object-fit: cover;width: 100% !important;" src="{{ asset('storage/'.strtolower(@$variation->printdesign->file)) }}"/>
                                                                        </div>
                                                                        @foreach($variation->fabrics as $fabric)
                                                                            <div style=" font-weight: 600; font-family: 'Raleway', sans-serif;flex: 0 0 40%;max-width: 40%; position: relative;width: 100%;padding-right: 15px;padding-left: 15px;">
                                                                                <p style=" font-weight: 600; font-family: 'Raleway', sans-serif; margin-bottom: 0 !important; margin-top: 0.5rem !important;">
                                                                                    <label for="">Print/Color:</label>
                                                                                    {{ @$fabric->printdesign->name }}
                                                                                    <a href="{{ route('thread.removeFabric', $fabric->id) }}">
                                                                                        <strong style=" font-weight: 600; font-family: 'Raleway', sans-serif;float: right !important;">
                                                                                            <i class="fa fa-times"></i>
                                                                                        </strong>
                                                                                    </a>
                                                                                </p>
                                                                                <img src="{{ asset('storage/'.strtolower(@$fabric->printdesign->file)) }}" height="120" style=" font-weight: 600; font-family: 'Raleway', sans-serif;object-fit: cover;width: 100% !important;">
                                                                            </div>
                                                                        @endforeach
                                                                        @if($variation->trim->count() > 0)
                                                                            @foreach($variation->trim as $trim)
                                                                                <div style=" font-weight: 600; font-family: 'Raleway', sans-serif;flex: 0 0 40%; max-width: 40%; position: relative;width: 100%;padding-right: 15px;padding-left: 15px;">
                                                                                    <p style=" font-weight: 600; font-family: 'Raleway', sans-serif; margin-bottom: 0 !important; margin-top: 0.5rem !important;">
                                                                                        <label for="">Trim:</label>
                                                                                        <a href="{{ route('thread.removeVariationTrim',$trim->id) }}">
                                                                                            <strong style=" font-weight: 600; font-family: 'Raleway', sans-serif;float: right !important;">
                                                                                                <i class="fa fa-times"></i>
                                                                                            </strong>
                                                                                        </a>
                                                                                    </p>
                                                                                    <img src="{{ asset(strtolower(@$trim->trim_image)) }}" height="120" style=" font-weight: 600; font-family: 'Raleway', sans-serif;object-fit: cover;width: 100% !important;">
                                                                                    <p style=" font-weight: 600; font-family: 'Raleway', sans-serif; margin-bottom: 0 !important; margin-top: 0.5rem !important;">
                                                                                        <label for="">
                                                                                            NOTES:{{@$trim->trim_note}}
                                                                                        </label>
                                                                                    </p>
                                                                                </div>
                                                                            @endforeach
                                                                        @endif
                                                                    </div>

                                                                    <div style=" font-weight: 600; font-family: 'Raleway', sans-serif;margin-bottom: 0.5rem !important; margin-top: 1rem !important;">
                                                                        <p style=" font-weight: 600; font-family: 'Raleway', sans-serif; margin-bottom: 0 !important; margin-top: 0.5rem !important;">
                                                                            <label for="">Fabric:</label>
                                                                            {{ @$variation->fabric->name }}
                                                                        </p>
                                                                        <p style=" font-weight: 600; font-family: 'Raleway', sans-serif;color: #000000 !important; margin: 0 !important; text-transform: uppercase !important; font-size: 12px !important;">
                                                                            <span for="">REG. Packs:</span>
                                                                            {{ $variation->regular_qty }} |
                                                                            <span style=" font-weight: 600; font-family: 'Raleway', sans-serif; color: #f36a5a;">
                                                                                Sku: {{ $variation->sku }}
                                                                            </span>
                                                                        </p>
                                                                        @if($variation->plus_sku)
                                                                            <p style=" font-weight: 600; font-family: 'Raleway', sans-serif;color: #000000 !important; margin: 0 !important; text-transform: uppercase !important; font-size: 12px !important;">
                                                                                <span for="">PLUS Packs:</span>
                                                                                {{ $variation->plus_qty }} |
                                                                                <span style=" font-weight: 600; font-family: 'Raleway', sans-serif; color: #f36a5a;">
                                                                                    Plus Sku: {{ $variation->plus_sku }}
                                                                                </span>
                                                                            </p>
                                                                        @endif
                                                                        <p style=" font-weight: 600; font-family: 'Raleway', sans-serif;color: #000000 !important; margin: 0 !important; text-transform: uppercase !important; font-size: 12px !important;">
                                                                            <span for="">Notes:</span> {{ $variation->notes ?? 'None' }}
                                                                        </p>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                @if($thread->thread_status == \Botble\Thread\Models\Thread::PRIVATE)
                                    <h6>PRIVATE LABEL SIZES</h6>
                                    <div style="display:flex;">
                                        @foreach($thread->regular_product_categories()->get() as $cat)
                                            @foreach($cat->category_sizes as $catSize)
                                                <div style=" margin: 0px 5px;background: #e8e8e8 !important; padding: 10px  !important;width: 65px  !important; border-radius: 5px  !important;    border: 1px solid #9a9a9a  !important;">
                                                    <label for="name">{{$catSize->full_name}}</label>
                                                    <p>{{get_pvt_cat_size_qty($thread->id,$cat->id,$catSize->id)}}</p>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @else
        <div id="DivIdToPrint">

            <table style="border: 1px solid #333; border-collapse: collapse; border-spacing: 0; width: 100%; margin-top: 15px;">
                <tbody>
                <tr>
                    <td style="border: 1px solid #333;text-align: center;" colspan="1" rowspan="3" class="tablelogo">
                        <img src="http://localhost/landb/public/images/lucky&amp;blessed_logo_sign_Black 1.png" alt="">
                    </td>
                    <td style="width: 12%; border: 1px solid #333; padding:10px;" colspan="1" rowspan="1">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif; margin: 0px;">
                            Order Date:<br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; ">{{ parse_date($thread->order_date) }}</span>
                        </p>
                    </td>
                    <td style="border: 1px solid #333;  padding:0px 10px;" rowspan="1" colspan="1">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">
                            Description <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; ">{{ $thread->name }}</span>
                        </p>
                    </td>
                    <td style="border: 1px solid #333;  padding:0px 10px;" colspan="1">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">
                            Designer: <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; "> {{ $thread->designer->first_name.' '.$thread->designer->last_name }}</span>
                        </p>
                    </td>
                    <td style="width: 8%; border: 1px solid #333; vertical-align: top;" colspan="1" rowspan="2"
                        class="p-0">
                        <div class="regpack">
                            <h6 style="font-size: 12px; margin:0px; background: #333;  font-family: 'Raleway', sans-serif; color: #fff; padding: 4px; text-align: center; text-transform: uppercase; font-weight: 400;">{{$thread->thread_status == \Botble\Thread\Models\Thread::PRIVATE ? 'Sizes' : 'Reg Pack Size Run'}}</h6>
                            @if($reg_cat)
                                @foreach($reg_cat->category_sizes as $key => $reg_catVal)
                                    <div class="sizediv">
                                        {{ $thread->thread_status == \Botble\Thread\Models\Thread::PRIVATE ? strtok($reg_catVal->name,'-') : $reg_catVal->full_name }}
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </td>
                    @if(!empty($plus_cat))
                        <td style="width: 8%;border: 1px solid #333; vertical-align: top;" colspan="1" rowspan="2"
                            class="p-0">
                            <div class="regpack">
                                <h6 style="font-size: 12px; margin:0px; background: #333;  font-family: 'Raleway', sans-serif; color: #fff; padding: 4px; text-align: center; text-transform: uppercase; font-weight: 400;">
                                    Plus Pack Size
                                </h6>
                                @if($plus_cat)
                                    @foreach($plus_cat->category_sizes as $key => $plus_catVal)
                                        <div class="sizediv">
                                            {{ $thread->thread_status == \Botble\Thread\Models\Thread::PRIVATE ? strtok($plus_catVal->name,'-') : $plus_catVal->name }}
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </td>
                    @endif
                    <td style="width: 13%;border: 1px solid #333;  padding:0px 10px;" rowspan="1" colspan="2">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">
                            PP Sample Due Date <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; "> {{ parse_date($thread->pp_sample_date) }}</span>
                        </p>
                    </td>
                    <td style="border: 1px solid #333;  padding:0px 10px;" colspan="2">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">
                            Request PP Sample: <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; ">{{ @$thread->pp_sample }}</span>
                        </p>
                    </td>
                    <td style="border: 1px solid #333;  padding:0px 10px;">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">
                            PP Sample Size: <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; "> {{ @$thread->pp_sample_size }} </span>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="width: 14%;border: 1px solid #333;  padding:0px 10px;">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">
                            Style # <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; ">  Reg Pack:  {{ $reg_sku }} <br>
                                @if(!empty($plus_sku))
                                    Plus Pack:  {{ $plus_sku }}
                                @endif
                            </span>
                        </p>
                    </td>
                    <td colspan="2" style="width: 14%;border: 1px solid #333;  padding:0px 10px;">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">
                            Category <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; ">
                                Reg Pack: {{ @$reg_cat->name }}<br>
                                <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; ">
                                    @if(!empty($plus_cat))
                                        Plus Pack: {{ $plus_cat->name }}
                                    @endif
                                </span>
                            </span>
                        </p>
                    </td>
                    <td style="border: 1px solid #333;  padding:0px 10px;" colspan="2">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">
                            Season: <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; ">  {{ @$thread->season->name }}</span>
                        </p>
                    </td>
                    <td style="border: 1px solid #333;  padding:0px 10px;" colspan="2">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">
                            Vendor: <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; ">{{ @$thread->vendor->first_name.' '.@$thread->vendor->last_name }}</span>
                        </p>
                    </td>
                    <td style="border: 1px solid #333;  padding:0px 10px;" colspan="1">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">
                            Status: <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; ">{{ $thread->thread_status }}</span>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid #333;  padding:0px 10px;" colspan="3">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">
                            Shipping Method: <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; "> {{ $thread->shipping_method }}</span>
                        </p>
                    </td>
                    <td style="border: 1px solid #333;  padding:0px 10px;" colspan="3">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">
                            Ship Date: <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; "> {{ parse_date($thread->ship_date) }}</span>
                        </p>
                    </td>
                    <td style="border: 1px solid #333;  padding:0px 10px;" colspan="4">
                        <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">
                            No Later Than <br>
                            <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; "> {{ parse_date($thread->cancel_date) }}</span>
                        </p>
                    </td>
                </tr>
                </tbody>
            </table>

            <div style="margin: 30px 0; border: solid 1px #333; padding: 15px; border-radius: 5px;">
                <div style="display: flex; margin-right: -15px; margin-left: -15px;">
                    <div style=" flex: 0 0 20.333333%; max-width: 20.333333%; position: relative; width: 100%; padding-right: 15px; padding-left: 15px;">
                        <h4 style=" text-align: center; font-size: 16px; text-transform: uppercase; margin: 0; font-family: 'Raleway', sans-serif;">Style</h4>
                        @if(!is_null($thread->spec_files))
                            @if(count($thread->spec_files))
                                <div style=" max-width: 1000px; position: relative; margin: auto; margin-top: 1.5rem !important;">
                                    @foreach($thread->spec_files as $file)
                                        <div>
                                            <img src="{{ asset($file->spec_file) }}" style="width:100%; height:310px;">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                        <br>
                    </div>
                    <div style="flex: 0 0 73.76667%; max-width: 73.76667%;position: relative; width: 100%; padding-right: 15px; padding-left: 15px;">
                        <div>
                            <h4 style="text-align: center; font-size: 16px; text-transform: uppercase; margin: 0; font-family: 'Raleway', sans-serif;">Order</h4>
                            <div>
                                <table style="border: 1px solid #333; border-collapse: collapse; height: 100%;border-spacing: 0; width: 100%; margin-top: 15px;">
                                    <tbody>
                                    <tr>
                                        <td style="border: 1px solid #333;  padding:0px 10px;">
                                            <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">
                                                Material:
                                                <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; ">{{ @$thread->material }}</span>
                                            </p>
                                        </td>
                                        <td style="border: 1px solid #333;  padding:0px 10px;" rowspan="2">
                                            <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">
                                                Label:
                                                <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; "> {{ @$thread->label }}</span>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #333;  padding:0px 10px;">
                                            <p style="font-size: 12px !important; font-weight: 600; font-family: 'Raleway', sans-serif;margin: 0px;">
                                                Sleeve Length:
                                                <span style="color: #f36a5a; text-transform: uppercase !important; font-weight: 400; ">{{ @$thread->sleeve }}</span>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px; vertical-align: top; font-size: 14px;" colspan="12">
                                            <div style="background: #f5f5f5; padding: 10px; border-radius: 5px;">
                                                <div style="display: flex;flex-wrap: wrap; margin-right: -15px; margin-left: -15px;">
                                                    @foreach($variations as $variation)
                                                        @if($variation->status == 'active' && $variation->is_denim == 0)
                                                            <div style=" flex: 0 0 44.333333%; max-width: 44.333333%; position: relative; width: 100%; padding-right: 15px; padding-left: 15px;">
                                                                <div style=" min-height: 445px; background: #ffffff;  border: 1px solid #d0d0d0;  border-radius: 10px; padding-left: 1rem !important; padding-right: 1rem !important; ">
                                                                    <h5 style=" margin-top: 0.5rem !important; font-family: 'Raleway', sans-serif;">
                                                                        {{$loop->iteration}}.
                                                                        Variation: {{ $variation->name }}
                                                                    </h5>
                                                                    <div style=" display: flex; flex-wrap:wrap;  margin-right: -15px; margin-left: -15px;">
                                                                        <div style=" min-height: 245px; flex: 0 0 40%;max-width: 40%; position: relative; width: 100%; padding-right: 15px;  padding-left: 15px;">
                                                                            <p style="margin-top: 0.5rem !important;margin-bottom: 0 !important; font-family: 'Raleway', sans-serif;">
                                                                                <label for="">Print/Color:</label>
                                                                                {{ @$variation->printdesign->name }}
                                                                            </p>
                                                                            <img style=" width: 70% !important;" src="{{ asset('storage/'.strtolower(@$variation->printdesign->file)) }}" height="120" width="120" style="object-fit: cover">
                                                                            <p style=" font-size: 12px !important; font-family: 'Raleway', sans-serif; margin:0px !important;">
                                                                                <span for="">Notes:</span>
                                                                                {{ $variation->notes ?? 'None' }}
                                                                            </p>
                                                                        </div>
                                                                        @if($variation->trim->count() > 0)
                                                                            @foreach($variation->trim as $trim)
                                                                                <div style=" flex: 0 0 40%;max-width: 40%; position: relative; width: 100%; padding-right: 15px;  padding-left: 15px;">
                                                                                    <p style="margin-top: 0.5rem !important;margin-bottom: 0 !important; font-family: 'Raleway', sans-serif;">
                                                                                        <label for="">Trim:</label>
                                                                                    </p>
                                                                                    <img style="object-fit: cover; width: 70% !important;" src="{{ asset(strtolower(@$trim->trim_image)) }}" height="120" width="100%">
                                                                                    <p style=" font-size: 12px !important; font-family: 'Raleway', sans-serif; margin:0px !important;">
                                                                                        <span for="">Notes:</span>
                                                                                        {{@$trim->trim_note}}
                                                                                    </p>
                                                                                </div>
                                                                            @endforeach
                                                                        @endif
                                                                    </div>

                                                                    <div style="margin-bottom: 0.5rem !important; margin-top: 5px !important !important;">
                                                                        <p style="font-size: 10px !important; text-transform: uppercase !important; margin: 0 !important; font-family: 'Raleway', sans-serif;">
                                                                            <span for="">REG. Packs:</span>
                                                                            {{ $variation->regular_qty }} |
                                                                            <span class="widget-title-color-red ">
                                                                                Sku: {{ $variation->sku }}
                                                                            </span>
                                                                        </p>
                                                                        <p style="font-size: 10px !important; text-transform: uppercase !important; margin: 0 !important; font-family: 'Raleway', sans-serif;">
                                                                            {{ $variation->plus_qty }} |
                                                                            <span class="widget-title-color-red">
                                                                                Plus Sku: {{ $variation->plus_sku }}
                                                                            </span>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                @if($thread->thread_status == \Botble\Thread\Models\Thread::PRIVATE)
                                    <h6>PRIVATE LABEL SIZES</h6>
                                    <div style="display:flex;">
                                        @foreach($thread->regular_product_categories()->get() as $cat)
                                            @if($cat)
                                                @foreach($cat->category_sizes as $catSize)
                                                    <div style=" margin: 0px 5px;background: #e8e8e8 !important; padding: 10px  !important;width: 65px  !important; border-radius: 5px  !important;    border: 1px solid #9a9a9a  !important;">
                                                        <label for="name">{{$catSize->full_name}}</label>
                                                        <p>{{get_pvt_cat_size_qty($thread->id,$cat->id,$catSize->id)}}</p>
                                                    </div>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endif
@endforeach
