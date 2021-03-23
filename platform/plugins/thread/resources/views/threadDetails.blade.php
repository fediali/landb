<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<?php $thread = $options['data']['thread']; ?>
<?php $variations = $options['data']['variations']; ?>
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button"
                role="tab" aria-controls="home" aria-selected="true">Details
        </button>
    </li>
</ul>
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">

        <div class="box box-widget widget-user">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="profile-area">
                <div class="row">
                    <div class="col-lg-2 col-4 mt-2 mb-2">
                        <div class="widget-user-image ml-2">
                            <img class="img-circle"
                                 src="http://laravel.landbw.co/api/resize/users/user.png?w=100&amp;h=100"
                                 alt="User Avatar">
                        </div>
                    </div>
                    <div class="col-lg-10 col-8  mt-2 mb-2">
                        <div class="widget-user-header bg-black">

                            <h3 class="widget-user-username widget-title-color-red font-bold profile-name">{{ $thread->designer->first_name.' '.$thread->designer->last_name }}</h3>
                            <h5 class="widget-user-desc text-light">Designer</h5>

                        </div>
                    </div>
                </div>
            </div>


            <div class="box-footer">
                <div class="row">
                    <div class="col-sm-4 border-right">
                        <div class="description-block">
                            <h5 class="description-header">0</h5>
                            <span class="description-text font-bold font-12">Category Count</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-4 border-right">
                        <div class="description-block">
                            <h5 class="description-header">{{ get_total_designs() }}</h5>
                            <span class="description-text font-bold font-12">Total Design</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-4">
                        <div class="description-block">
                            <h5 class="description-header">{{ get_approved_designs() }}</h5>
                            <span class="description-text font-bold font-12">Approved Design</span>
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
                                                        <td style="border-top: none" class="order font-bold font-12">ORDER
                                                            #:
                                                        </td>
                                                        <td style="border-top: none">PP Sample Due
                                                            Date: {{ $thread->pp_sample_date->toDateString() }}
                                                        </td>
                                                        <td class="font-bold font-12" style="border-right: none;">
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
                                <td colspan="1" rowspan="2" class="tablelogo"><img
                                        src="{{ asset('images/lucky&blessed_logo_sign_Black 1.png') }}" alt=""></td>
                                <td colspan="1" rowspan="1"><p class="font-bold font-12">Order#:<br> <span
                                            class="widget-title-color-red"> {{ $thread->order_no }}</span></p></td>
                                <td rowspan="1" colspan="3"><p class="font-bold font-12">Description <br> <span
                                            class="widget-title-color-red text-uppercase">{{ $thread->name }}</span></p>
                                </td>
                                <td rowspan="1" colspan="2"><p class="font-bold font-12">PP Sample Due Date <br><span
                                            class="widget-title-color-red"> {{ parse_date($thread->pp_sample_date) }}</span>
                                    </p></td>
                                <td colspan="1" rowspan="2" class="p-0">
                                    <div class="regpack">
                                        <h6>Reg Size Run</h6>
                                        @foreach($options['data']['reg_cat']->category_sizes as $key => $reg_cat)
                                            <div class="sizediv">
                                                {{ $reg_cat->name }}
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                @if(!empty($options['data']['plus_cat']))
                                    <td colspan="1" rowspan="4" class="p-0">
                                        <div class="regpack">
                                            <h6>Plus Size Run</h6>

                                            @foreach($options['data']['plus_cat']->category_sizes as $key => $plus_cat)
                                                <div class="sizediv">
                                                    {{ $plus_cat->name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                @endif
                                <td colspan="1"><p class="font-bold font-12">Designer: <br><span
                                            class="widget-title-color-red text-uppercase"> {{ $thread->designer->first_name.' '.$thread->designer->last_name }}</span>
                                    </p></td>
                                <td colspan="1"><p class="font-bold font-12">Vendor: <br> <span
                                            class="widget-title-color-red text-uppercase">{{ @$thread->vendor->first_name.' '.@$thread->vendor->last_name }}</span>
                                    </p></td>
                                <td colspan="1"><p class="font-bold font-12">Status: <br> <span
                                            class="widget-title-color-red text-uppercase">{{ $thread->status }}</span>
                                    </p></td>


                            </tr>
                            <tr>
                                <td colspan="1" rowspan="1"><p class="font-bold font-12">Order Date: <span
                                            class="widget-title-color-red text-uppercase">{{ parse_date($thread->order_date) }}</span>
                                    </p></td>
                                <td><p class="font-bold font-12">Style # <br><span
                                            class="widget-title-color-red text-uppercase"> Reg:  {{ $options['data']['reg_sku'] }} <br>@if(!empty($options['data']['plus_sku']))
                                                Plus:  {{ $options['data']['plus_sku'] }} @endif</span></p></td>
                                <td><p class="font-bold font-12">Category <br><span
                                            class="widget-title-color-red text-uppercase">
                                    Reg: {{ $options['data']['reg_cat']->name }}<br><span
                                                class="widget-title-color-red text-uppercase">
                                  @if(!empty($options['data']['plus_cat']))
                                                    Plus: {{ $options['data']['plus_cat']->name }} @endif</span></p>
                                </td>
                                <td><p class="font-bold font-12">Season: <br><span
                                            class="widget-title-color-red text-uppercase"> {{ @$thread->season->name }}</span>
                                    </p></td>
                                <td><p class="font-bold font-12">Request PP Sample: <br> <span
                                            class="widget-title-color-red text-uppercase">{{ @$thread->pp_sample }}</span>
                                    </p></td>
                                <td><p class="font-bold font-12">PP Sample Size: <br><span
                                            class="widget-title-color-red text-uppercase"> {{ @$thread->pp_sample_size }}</span>
                                    </p></td>
                                <td><p class="font-bold font-12">Shipping Method: <br><span
                                            class="widget-title-color-red text-uppercase"> {{ $thread->shipping_method }}</span>
                                    </p></td>
                                <td><p class="font-bold font-12">Ship Date: <br><span
                                            class="widget-title-color-red text-uppercase"> {{ parse_date($thread->ship_date) }}</span>
                                    </p></td>
                                <td><p class="font-bold font-12">No Later Than <br><span
                                            class="widget-title-color-red text-uppercase"> {{ parse_date($thread->cancel_date) }}</span>
                                    </p></td>

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
                                                    <img height="100px" width="140px"
                                                         src="{{ asset('storage/'.$thread->spec_file) }}"
                                                         style=" object-fit: cover;">
                                                @endif
                                                <div class="container">
                                                    <div id="myCarousel" class="carousel slide" data-ride="carousel">
                                                        <!-- Indicators -->
                                                        <ol class="carousel-indicators">
                                                            <li data-target="#myCarousel" data-slide-to="0"
                                                                class="active"></li>
                                                            <li data-target="#myCarousel" data-slide-to="1"></li>
                                                            <li data-target="#myCarousel" data-slide-to="2"></li>
                                                        </ol>

                                                        <!-- Wrapper for slides -->
                                                        <div class="carousel-inner">
                                                            <div class="item active">
                                                                <img
                                                                    src="http://localhost/landb/public/storage/image-1.jpg"
                                                                    alt="Los Angeles" style="width:100%;">
                                                            </div>

                                                            <div class="item">
                                                                <img
                                                                    src="http://localhost/landb/public/storage/image-1.jpg"
                                                                    alt="Chicago" style="width:100%;">
                                                            </div>

                                                            <div class="item">
                                                                <img
                                                                    src="http://localhost/landb/public/storage/image-1.jpg"
                                                                    alt="New york" style="width:100%;">
                                                            </div>
                                                        </div>

                                                        <!-- Left and right controls -->
                                                        <a class="left carousel-control" href="#myCarousel"
                                                           data-slide="prev">
                                                            <span class="glyphicon glyphicon-chevron-left"></span>
                                                            <span class="sr-only">Previous</span>
                                                        </a>
                                                        <a class="right carousel-control" href="#myCarousel"
                                                           data-slide="next">
                                                            <span class="glyphicon glyphicon-chevron-right"></span>
                                                            <span class="sr-only">Next</span>
                                                        </a>
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-8">
                                @if($thread->is_denim == 1)
                                    <div class="specificationwrap">
                                        <h4>Specifications</h4>
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
                                                                            <label for=""> {{ $fit }}</label> <input
                                                                                type="checkbox"
                                                                                disabled {!! ($key == $thread->fit_id) ? 'checked' : '' !!}>
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
                                                                            <label for=""> {{ $rise }}</label> <input
                                                                                type="checkbox"
                                                                                disabled {!! ($key == $thread->fit_id) ? 'checked' : '' !!}>
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
                                                                            <label for=""> {{ $fabric }}</label> <input
                                                                                type="checkbox"
                                                                                disabled {!! ($key == $thread->fit_id) ? 'checked' : '' !!}>
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
                                                            <b>Fabric Print
                                                                Direction: </b>{{ $thread->fabric_print_direction }}
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="12">
                                                        <div class="tabrow ">
                                                            <b>Additional Notes: </b>{{ $thread->description }}
                                                        </div>
                                                        <div class="d-flex">
                                                            <img style="height: 400px; width: 400px;"
                                                                 src="http://localhost/landb/public/storage/jeans.png"/>
                                                            <img style="height: 400px; width: 400px;"
                                                                 src="http://localhost/landb/public/storage/jeans.png"/>
                                                            <img style="height: 400px; width: 400px;"
                                                                 src="http://localhost/landb/public/storage/jeans.png"/>
                                                            <img style="height: 400px; width: 400px;"
                                                                 src="http://localhost/landb/public/storage/jeans.png"/>
                                                            <img style="height: 400px; width: 400px;"
                                                                 src="http://localhost/landb/public/storage/jeans.png"/>
                                                            <img style="height: 400px; width: 400px;"
                                                                 src="http://localhost/landb/public/storage/jeans.png"/>
                                                            <img style="height: 400px; width: 400px;"
                                                                 src="http://localhost/landb/public/storage/jeans.png"/>
                                                            <img style="height: 400px; width: 400px;"
                                                                 src="http://localhost/landb/public/storage/jeans.png"/>
                                                            <img style="height: 400px; width: 400px;"
                                                                 src="http://localhost/landb/public/storage/jeans.png"/>
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

                                                    <p class="font-bold font-12"> Fabric: <span
                                                            class="widget-title-color-red text-uppercase">{{ @$thread->material }}</span>
                                                    </p>

                                                    <td rowspan="2"><p class="font-bold font-12">Label:<span
                                                                class="widget-title-color-red text-uppercase"> {{ @$thread->label }}</span>
                                                        </p></td>
                                                </tr>
                                                <tr>
                                                    <td><p class="font-bold font-12">Sleeve Length: <span
                                                                class="widget-title-color-red text-uppercase">{{ @$thread->sleeve }}</span>
                                                        </p></td>
                                                </tr>
                                                <tr>
                                                {{-- <td colspan="12">--}}
 {{--                                                       <div class="order-box mb-2 mt-2">--}}
 {{--                                                           <div class="box w-100">--}}
 {{--                                                               <h6>Fedi--}}
  {{--                                                                  <button type="button"
                                                                            class="btn btn-warning add_print"
                                                                            data-toggle="modal"
                                                                            data-target="#modal-default"
                                                                            data-id="s"
                                                                            data-name="test">--}}
 {{--                                                                       <i class="fa fa-plus"></i>--}}
  {{--                                                                  </button>--}}
  {{--                                                              </h6>--}}
   {{--                                                         </div>--}}
   {{--                                                     </div>--}}
     {{--                                               </td>--}}
{{--                                                    <td colspan="12">--}}
{{--                                                        <div class="order-box mb-2 mt-2">--}}
{{--                                                            @foreach($variations as $variation)--}}
{{--                                                                @if($variation->is_denim == 0 && $variation->status == 'active')--}}
{{--                                                                    <div class="box w-100">--}}
{{--                                                                        <h6>{{ $variation->name }}--}}
{{--                                                                            <button type="button"--}}
{{--                                                                                    class="btn btn-warning add_print"--}}
{{--                                                                                    data-toggle="modal"--}}
{{--                                                                                    data-target="#modal-default"--}}
{{--                                                                                    data-id="{{ $variation->id }}"--}}
{{--                                                                                    data-name="test">--}}
{{--                                                                                <i class="fa fa-plus"></i>--}}
{{--                                                                            </button>--}}
{{--                                                                        </h6>--}}
{{--                                                                    </div>--}}
{{--                                                                @endif--}}
{{--                                                            @endforeach--}}

{{--                                                            @foreach($variations as $variation)--}}
{{--                                                                @if($variation->status == 'active')--}}
{{--                                                                    <div class="box row d-mt-block">--}}
{{--                                                                        <div class="col-lg-6">--}}
{{--                                                                            <div--}}
{{--                                                                                class="variationdiv variation-div pl-3 pr-3 mb-3">--}}
{{--                                                                                <h5 class=" mt-2">--}}
{{--                                                                                    Variation: {{ @$variation->name }}</h5>--}}
{{--                                                                                <p><label--}}
{{--                                                                                        for="">Fabric:</label>{{ @$variation->printdesign->name }}--}}
{{--                                                                                </p>--}}
{{--                                                                                <img class="w-100"--}}
{{--                                                                                     src="{{ asset('storage/'.$variation->printdesign->file) }}"--}}
{{--                                                                                     height="120" width="120"--}}
{{--                                                                                     style="object-fit: cover">--}}
{{--                                                                                <div class="mt-2 mb-2">--}}
{{--                                                                                    <p class="text-black"><span for="">REG. Packs:</span> {{ $variation->regular_qty }}--}}
{{--                                                                                        | <span--}}
{{--                                                                                            class="widget-title-color-red">Sku: {{ $variation->sku }} </span>--}}
{{--                                                                                    </p>--}}
{{--                                                                                    <p class="text-black"><span for="">PLUS Packs:</span> {{ $variation->plus_qty }}--}}
{{--                                                                                        | <span--}}
{{--                                                                                            class="widget-title-color-red"> Plus Sku: {{ $variation->plus_sku }}</span>--}}
{{--                                                                                    </p>--}}
{{--                                                                                </div>--}}
{{--                                                                            </div>--}}
{{--                                                                        </div>--}}
{{--                                                                        @foreach($variation->fabrics as $fabric)--}}
{{--                                                                            <div class="col-lg-6">--}}
{{--                                                                                <div--}}
{{--                                                                                    class="variationdiv variation-div pl-3 pr-3  mb-3">--}}
{{--                                                                                    <h5 class="mt-2">--}}
{{--                                                                                        Variation: {{ @$fabric->name }}--}}
{{--                                                                                        <a href="{{ route('thread.removeFabric', ['id' => $fabric->id]) }}"><i--}}
{{--                                                                                                class="float-right fa fa-times"></i></a>--}}
{{--                                                                                    </h5>--}}
{{--                                                                                    <p><label--}}
{{--                                                                                            for="">Fabric:</label>{{ @$fabric->printdesign->name }}--}}
{{--                                                                                    </p>--}}
{{--                                                                                    <img class="w-100"--}}
{{--                                                                                         src="{{ asset('storage/'.$fabric->printdesign->file) }}"--}}
{{--                                                                                         height="120" width="120"--}}
{{--                                                                                         style="object-fit: cover">--}}
{{--                                                                                    <div>--}}
{{--                                                                                        <p class="text-black"><span--}}
{{--                                                                                                for="">REG. Packs:</span> {{ $variation->regular_qty }}--}}
{{--                                                                                            | <span--}}
{{--                                                                                                class="widget-title-color-red"> Sku: {{ $variation->sku }}</span>--}}
{{--                                                                                        </p>--}}
{{--                                                                                        <p class="text-black"><span--}}
{{--                                                                                                for="">PLUS Packs:</span> {{ $variation->plus_qty }}--}}
{{--                                                                                            | <span--}}
{{--                                                                                                class="widget-title-color-red"> Plus Sku: {{ $variation->plus_sku }}</span>--}}
{{--                                                                                        </p>--}}
{{--                                                                                    </div>--}}
{{--                                                                                </div>--}}
{{--                                                                            </div>--}}
{{--                                                                        @endforeach--}}
{{--                                                                    </div>--}}
{{--                                                                @endif--}}
{{--                                                            @endforeach--}}

{{--                                                        </div>--}}
{{--                                                    </td>--}}

<td colspan="12">
<div class="order-box mb-2 mt-2">


                                                            <div class="box w-100">
                                                                 <h6>Osama Ali
                                                                    <button type="button" class="btn btn-warning add_print" data-toggle="modal" data-target="#modal-default" data-id="7" data-name="test">
                                                                         <i class="fa fa-plus"></i>
                                                                     </button>
                                                                 </h6>
                                                             </div>

                                                             <div class="box row d-mt-block">
                                                                 <div class="col-lg-12">
                                                                     <div class="variationdiv variation-div pl-3 pr-3 mb-3">
                                                                         <h5 class=" mt-2">
                                                                             Variation: Osama Ali</h5>
                                                                        <div class="row">
                                                                            <div class="col-lg-6">
                                                                            <p class="mb-0 mt-2"><label for="">Fabric:</label>asd
                                                                         </p>
                                                                         <img class="w-100" src="http://localhost/landb/public/storage/image-1.jpg" height="120" width="120" style="object-fit: cover">
                                                                            </div>
                                                                            <div class="col-lg-6">
                                                                            <p class="mb-0 mt-2"><label for="">Fabric:</label>asd
                                                                         </p>
                                                                         <img class="w-100" src="http://localhost/landb/public/storage/image-1.jpg" height="120" width="120" style="object-fit: cover">
                                                                            </div>
                                                                        </div>

                                                                         <div class="mt-3 mb-2">
                                                                            <p class="text-black font-12 text-uppercase m-0"><span for="">REG. Packs:</span> 2
                                                                                 | <span class="widget-title-color-red ">Sku: OHO914ASD </span>
                                                                             </p>
                                                                             <p class="text-black font-12 text-uppercase m-0"><span for="">PLUS Packs:</span> 2
                                                                                 | <span class="widget-title-color-red"> Plus Sku: </span>
                                                                             </p>
                                                                        </div>
                                                                     </div>
                                                                 </div>
                                                                 <div class="col-lg-6">
                                                                     <div class="variationdiv variation-div pl-3 pr-3 mb-3">
                                                                         <h5 class=" mt-2">
                                                                             Variation: Osama Ali</h5>
                                                                         <p  class="mb-0 mt-2"><label for="">Fabric:</label>asd
                                                                         </p>
                                                                         <img class="w-100" src="http://localhost/landb/public/storage/image-1.jpg" height="120" width="120" style="object-fit: cover">
                                                                         <div class="mt-2 mb-2">
                                                                         <p class="text-black font-12 text-uppercase m-0"><span for="">REG. Packs:</span> 2
                                                                                 | <span class="widget-title-color-red ">Sku: OHO914ASD </span>
                                                                             </p>
                                                                             <p class="text-black font-12 text-uppercase m-0"><span for="">PLUS Packs:</span> 2
                                                                                 | <span class="widget-title-color-red"> Plus Sku: </span>
                                                                             </p>
                                                                        </div>
                                                                     </div>
                                                                 </div>
                                                                 <div class="col-lg-6">
                                                                     <div class="variationdiv variation-div pl-3 pr-3 mb-3">
                                                                         <h5 class=" mt-2">
                                                                             Variation: Osama Ali</h5>
                                                                         <p  class="mb-0 mt-2"><label for="">Fabric:</label>asd
                                                                         </p>
                                                                         <img class="w-100" src="http://localhost/landb/public/storage/image-1.jpg" height="120" width="120" style="object-fit: cover">
                                                                         <div class="mt-2 mb-2">
                                                                         <p class="text-black font-12 text-uppercase m-0"><span for="">REG. Packs:</span> 2
                                                                                 | <span class="widget-title-color-red ">Sku: OHO914ASD </span>
                                                                             </p>
                                                                             <p class="text-black font-12 text-uppercase m-0"><span for="">PLUS Packs:</span> 2
                                                                                 | <span class="widget-title-color-red"> Plus Sku: </span>
                                                                             </p>
                                                                        </div>
                                                                     </div>
                                                                 </div>
                                                             </div>



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

<link rel="stylesheet" href="{{ asset('css/style.css') }}"/>
<style>
    .details-table td {
        border: 1px solid black;
    }
</style>


