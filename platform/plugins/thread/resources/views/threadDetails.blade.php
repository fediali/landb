<?php $thread = $options['data']['thread']; ?>
<?php $variations = $options['data']['variations']; ?>
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Details</button>
    </li>
</ul>
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">

        <div class="box box-widget widget-user">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-black">

                <h3 class="widget-user-username">{{ $thread->designer->first_name.' '.$thread->designer->last_name }}</h3>
                <h5 class="widget-user-desc">Designer</h5>

            </div>
            <div class="widget-user-image">
                <img class="img-circle" src="http://laravel.landbw.co/api/resize/users/user.png?w=100&amp;h=100" alt="User Avatar">
            </div>
            <div class="box-footer">
                <div class="row">
                    <div class="col-sm-4 border-right">
                        <div class="description-block">
                            <h5 class="description-header">0</h5>
                            <span class="description-text">Category Count</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-4 border-right">
                        <div class="description-block">
                            <h5 class="description-header">0</h5>
                            <span class="description-text">Total Design</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-4">
                        <div class="description-block">
                            <h5 class="description-header">0</h5>
                            <span class="description-text">Approved Design</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            {{--<div class="row">
                <div class="col-md-12">
                    <!-- Box Comment -->
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title">Tech Pack</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="tech-pack-table">
                                <section class="details-table">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-sm-6 boxes1">
                                                <table class="table first1">
                                                    <thead>
                                                    <tr>
                                                        <td style="border-top: none" class="blank">
                                                            <img style="height: 150px;" src="http://laravel.landbw.co/storage/app/public/logo-tech-pack.png">
                                                        </td>
                                                        <td style="border-top: none" class="order">ORDER
                                                            #:
                                                        </td>
                                                        <td style="border-top: none">PP Sample Due
                                                            Date: {{ $thread->pp_sample_date->toDateString() }}
                                                        </td>
                                                        <td style="border-right: none;">
                                                            DESCRIPTION: {{ $thread->description }}
                                                        </td>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td class="custom-border">Lucky &amp; Blessed</td>
                                                        <td class="custom-border">STYLE
                                                            #: {{ $thread->sku }}</td>
                                                        <td class="custom-border">
                                                            Category:
                                                        @if(@$thread->product_categories)
                                                            @foreach($thread->product_categories as $category)
                                                                {{ $loop->iteration.')- '. $category->name }}<br>
                                                            @endforeach
                                                        @endif
                                                        </td>
                                                        <td class="custom-border">
                                                            Season: {{ @$thread->season->name }}
                                                        </td>
                                                        <td class="custom-border">Request PP
                                                            Sample: {{ @$thread->pp_sample }}
                                                        </td>
                                                        <td class="custom-border">PP Sample
                                                            Size: {{ @$thread->pp_sample_size }}
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="col-sm-2 boxes2">
                                                <table class="table middle">
                                                    <thead>
                                                    <tr>
                                                        <th style="border-top: none;" class="pack-size">REG SIZE RUN</th>
                                                        <th style="border-top: none;" class="pack-size1">PLUS SIZE RUN</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td>
                                                            <span class="border-crops">S-2</span><br>
                                                            <span class="border-crops">M-2</span><br>
                                                            <span class="border-crops">L-2</span><br>
                                                        </td>
                                                        <td style="border-right: none">
                                                            <span class="border-crops">XL-2</span><br>
                                                            <span class="border-crops">2XL-2</span><br>
                                                            <span class="border-crops">3XL-2</span><br>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="col-sm-4 boxes3">
                                                <table class="table last-col">
                                                    <thead>
                                                    <tr style="border-right: none;">
                                                        <td style="border-top: none;">
                                                            Designer: {{ $thread->designer->first_name.' '.$thread->designer->last_name }}
                                                        </td>
                                                        <td style="border-top: none;">
                                                            Vendor: {{ @$thread->vendor->first_name.' '.@$thread->vendor->last_name }}
                                                        </td>
                                                        <td style="border-top: none; border-.col-sm-6.box2: none;">
                                                            Status: {{ $thread->status }}
                                                        </td>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td>Shipping
                                                            Method:
                                                            {{ $thread->shipping_method }}
                                                        </td>
                                                        <td>
                                                            Ship Date:
                                                            {{ $thread->ship_date }}
                                                        </td>
                                                        <td style="border-right: none">
                                                            No Later Than:
                                                            {{ $thread->cancel_date }}
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </section></div>


                            <section class="table-2">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <h4>STYLE</h4>
                                            <div class="row">
                                                <div class="col-sm-12 style">
                                                    <p>
                                                    </p>
                                                </div>
                                            </div>
                                            <br>
                                        </div>

                                        <div class="col-sm-8">
                                            <h4>ORDER</h4>
                                            <div style="border: 1px solid;" class="row specic-box">
                                                <div class="col-sm-6 box">
                                                    <h6>Fabric: </h6>
                                                </div>
                                                <div class="col-sm-6 box1"><h6 class="label-box">
                                                        LABEL: Shirt</h6></div>
                                                <div class="col-sm-6 box2">
                                                    <h6>Sleeve Length: Long</h6>
                                                </div>
                                                <div style="border-bottom: 1px solid;" class="col-sm-6"></div>

                                                <div class="col-sm-12">
                                                    <div style="padding: 2%;" class="row">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                            </section>
                        </div>
                    </div>
                </div>
            </div>--}}

            <section class="denim_table new_clothing">
                <div class="container">
                    <div class="table-responsive">
                        <table>

                            <tbody>
                            <tr>
                                <td colspan="1" rowspan="2" class="tablelogo"><img src="{{ asset('images/lucky&blessed_logo_sign_Black 1.png') }}" alt=""></td>
                                <td colspan="1" rowspan="1">Order#: <br>  </td>
                                <td rowspan="1" colspan="3">Description <br> {{ $thread->description }}</td>
                                <td rowspan="1" colspan="2">PP Sample Due Date <br> {{ $thread->pp_sample_date->toDateString() }}</td>
                                <td colspan="1" rowspan="2">
                                    <div class="regpack">
                                        <h6>Reg Size Run</h6>
                                        @foreach($options['data']['reg_cat']->category_sizes as $key => $reg_cat)
                                            <div class="sizediv">
                                                {{ $reg_cat->name }}
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td colspan="1" rowspan="4">
                                    <div class="regpack">
                                        <h6>Plus Size Run</h6>
                                        @foreach($options['data']['plus_cat']->category_sizes as $key => $plus_cat)
                                            <div class="sizediv">
                                                {{ $plus_cat->name }}
                                            </div>
                                        @endforeach

                                    </div>
                                </td>
                                <td colspan="1">Designer: <br> {{ $thread->designer->first_name.' '.$thread->designer->last_name }}</td>
                                <td colspan="1">Vendor: <br> {{ @$thread->vendor->first_name.' '.@$thread->vendor->last_name }}</td>
                                <td colspan="1">Status: <br> {{ $thread->status }}</td>


                            </tr>
                            <tr>
                                <td colspan="1" rowspan="1">Order Date: </td>
                                <td>Style # <br>  {{ $thread->sku }}</td>
                                <td>Category <br>
                                    @if(@$thread->product_categories)
                                        @foreach($thread->product_categories as $category)
                                            {{ $loop->iteration.')- '. @$category->name }}<br>
                                        @endforeach
                                    @endif</td>
                                <td>Season: <br> {{ @$thread->season->name }}</td>
                                <td>Request PP Sample: <br> {{ @$thread->pp_sample }}</td>
                                <td>PP Sample Size: <br> {{ @$thread->pp_sample_size }}</td>
                                <td>Shipping Method: <br> {{ $thread->shipping_method }}</td>
                                <td>Ship Date: <br> {{ $thread->ship_date }}</td>
                                <td>No Later Than <br> {{ $thread->cancel_date }}</td>

                            </tr>



                            </tbody>
                        </table>
                    </div>
                    <div class="style_specification">
                        <div class="row ">
                            <div class="col-md-4 ">
                                <h4>Style</h4>
                                <div class="stylebox table-responsive">
                                    <table>
                                        <tbody>
                                        <tr>
                                            <td>

                                                @if(!empty($thread->spec_file))
                                                <img height="100px" width="140px" src="{{ asset('storage/'.$thread->spec_file) }}" style=" object-fit: cover;">
                                                @endif

                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-8">
                                @if($thread->is_denim == 1)
                                    <div class="specificationwrap">
                                        <h4>Secifications</h4>
                                        <div class="table-responsive">
                                            <table>
                                                <thead>
                                                <tr>
                                                    <th colspan="1" rowspan="1">Inseam: {{ $thread->inseam }}</th>
                                                    <th colspan="1" rowspan="1">Label: {{ @$thread->name }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>

                                                    <td colspan="12">
                                                        <div class="tabrow">
                                                            <b>Fit</b>
                                                            @foreach(array_chunk($options['data']['fits'], 5, true) as $fits)
                                                                <div class="item">
                                                                    @foreach($fits as $key => $fit)
                                                                        <div class="checkbox">
                                                                            <label for=""> {{ $fit }}</label> <input type="checkbox" disabled {!! ($key == $thread->fit_id) ? 'checked' : '' !!}>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td colspan="12">
                                                        <div class="tabrow">
                                                            <b>Rise</b>
                                                            @foreach(array_chunk($options['data']['rises'], 1, true) as $rises)
                                                                <div class="item">
                                                                    @foreach($rises as $key => $rise)
                                                                        <div class="checkbox">
                                                                            <label for=""> {{ $rise }}</label> <input type="checkbox" disabled {!! ($key == $thread->fit_id) ? 'checked' : '' !!}>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endforeach
                                                        </div>



                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td colspan="12">
                                                        <div class="tabrow">
                                                            <b>Fabric:</b>
                                                            @foreach(array_chunk($options['data']['fabrics'], 1, true) as $fabrics)
                                                                <div class="item">
                                                                    @foreach($fabrics as $key => $fabric)
                                                                        <div class="checkbox">
                                                                            <label for=""> {{ $fabric }}</label> <input type="checkbox" disabled {!! ($key == $thread->fit_id) ? 'checked' : '' !!}>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endforeach
                                                        </div>



                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="12">
                                                        <div class="tabrow">
                                                            <b>Fabric Print Direction: </b>{{ $thread->fabric_print_direction }}
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="12">
                                                        <div class="tabrow">
                                                            <b>Wash: </b>
                                                            @foreach(array_chunk($options['data']['washes'], 5, true) as $washes)
                                                                <div class="item">
                                                                    @foreach($washes as $key => $wash)
                                                                        <div class="checkbox">
                                                                            <label for=""> {{ $wash }}</label> <input type="checkbox" disabled {!! ($key == $thread->wash_id) ? 'checked' : '' !!}>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="12">
                                                        <div class="tabrow additionalnote">
                                                            <b>Additional Notes: </b>{{ $thread->description }}
                                                        </div>
                                                    </td>
                                                </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @else
                                    <div class="specificationwrap">
                                        <h4>Order</h4>
                                        <div class="table-responsive">
                                            <table>

                                                <tbody>
                                                <tr>
                                                    <td>
                                                        Fabric: {{ @$thread->material }}
                                                    </td>
                                                    <td rowspan="2">Additional Notes: {{ @$thread->label }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Sleeve Length: {{ @$thread->sleeve }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="12">
                                                        <div class="orderbox_wrap">
                                                            @foreach($variations as $variation)
                                                                <div class="box">
                                                                    <h6>{{ $variation->name }} <button type="button" class="btn btn-warning add_print" data-toggle="modal" data-target="#modal-default" data-id="{{ $variation->id }}" data-name="test">
                                                                            <i class="fa fa-plus"></i>
                                                                        </button></h6>
                                                                </div>
                                                            @endforeach

                                                            @foreach($variations as $variation)
                                                                @foreach($variation->fabrics as $fabric)
                                                                    <div class="box">
                                                                        {{--<h6>Variation: {{ $variation->name }}</h6>--}}
                                                                        <div class="variationdiv">
                                                                            <h5>Variation: {{ @$variation->name }} <a href="{{ route('thread.removeFabric', ['id' => $fabric->id]) }}"><i class="float-right fa fa-times"></i></a></h5>
                                                                            <p><label for="">Fabric:</label>{{ @$fabric->printdesign->name }}</p>
                                                                            <img src="{{ asset('storage/'.$fabric->printdesign->file) }}" height="120" width="120" style="object-fit: cover">
                                                                            <div class="reg_bottom">
                                                                                <p><label for="">REG. Packs:</label> {{ $variation->regular_qty }} | Sku: {{ $variation->sku }}</p>
                                                                                <p><label for="">PLUS Packs:</label> {{ $variation->plus_qty }} | Plus Sku: {{ $variation->plus_sku }}</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @endforeach

                                                        </div>
                                                    </td>
                                                </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

    </div>
</div>

<link rel="stylesheet" href="{{ asset('css/style.css') }}" />
<style>
    .details-table td{
        border: 1px solid black;
    }
</style>

<script>

</script>
