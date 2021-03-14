<?php $thread = $options['data']['thread']; ?>
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Details</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Vendor Discussions</button>
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




            <div class="row">
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
                            <a href="javascript:void(0)" class="btn btn-secondary" data-toggle="modal" data-target="#add_variation"> Add Variation</a>
                            <a href="javascript:void(0)" class="btn btn-secondary" data-toggle="modal" data-target="#view_variation"> View Variations</a>
                        </div>

                        <div class="modal fade in" id="modal-default" style="display: none; padding-right: 17px;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span></button>
                                        <h4 class="modal-title">Add More Fabric to <span class="variation-name"></span></h4>
                                    </div>
                                    <form method="POST" action="http://laravel.landbw.co/admin/thread-variation-prints" accept-charset="UTF-8" enctype="multipart/form-data"><input name="_token" type="hidden" value="rJ72m23pRe146zGHzXsY3NJBWgGge99m1MquMyhK">
                                        <div class="modal-body">
                                            <p>
                                                <label for="name">Variation Name:</label>
                                                <input class="form-control" placeholder="Enter Variation Name" name="name" type="text" id="name">
                                                <label for="print_id">Print / Solid:</label>
                                                <select class="form-control" id="print_id" name="print_id"><option selected="selected" value="">Enter Print</option></select>
                                                <input class="form-control thread_variation_id" placeholder="Enter Print" name="thread_variation_id" type="hidden">
                                                <input class="form-control" placeholder="Enter Print" name="input" type="hidden" value="1">
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                                            <input class="btn btn-primary" type="submit" value="Save">
                                        </div>
                                    </form>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                        <div class="modal fade in" id="add_variation" style="display: none; padding-right: 17px;">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span></button>
                                        <h4 class="modal-title">Adding Variations<span class="variation-name"></span></h4>
                                    </div>
                                    <form method="POST" action="{{ URL::to('/admin/threads/add-variations') }}" accept-charset="UTF-8" enctype="multipart/form-data">
                                        @csrf
                                        <input name="thread_id" type="hidden" value="33">
                                        <div class="modal-body">
                                            <table class="table table-striped table-bordered">

                                                <tbody id="multi-variations">
                                                <tr>
                                                    <td style="width: 13%;">
                                                        <button type="button" class="add_row form-control btn btn-secondary" id="add-new"><i class="fa fa-plus"></i> Add:</button>
                                                    </td>
                                                </tr>
                                                <tr class="duplicate" id="duplicate-variation">
                                                    <td width="10%">
                                                        <label for="name">Name:</label>
                                                        <input class="form-control" placeholder="Add Name" name="name[]" type="text">
                                                    </td>

                                                    <td width="20%">
                                                        <label for="print_id">Print / Solid:</label>
                                                        <select class="form-control select" name="print_id[]">
                                                            <option selected="selected" value="">Select Print</option>
                                                            @foreach($options['data']['printdesigns'] as $key => $print)
                                                                <option value="{{ $key }}">{{ $print }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td width="15%">
                                                        <label for="pack_id">Regular:</label>
                                                        <input class="form-control" placeholder="Add Regular Quantity" name="regular_qty[]" type="text">
                                                    </td>
                                                    <td width="10%">
                                                        <label for="pack_id">Plus:</label>
                                                        <input class="form-control" placeholder="Add Plus Quantity" name="plus_qty[]" type="text">
                                                    </td>
                                                    <td width="15%">
                                                        <label for="cost">Cost:</label>
                                                        <input class="form-control" placeholder="Add Cost" name="cost[]" type="text">
                                                    </td>
                                                    <td width="15%">
                                                        <label for="Notes">Notes:</label>
                                                        <textarea class="form-control" placeholder="Add Notes" name="notes[]" cols="50" rows="2"></textarea>
                                                    </td>
                                                    <td width="10%">

                                                        <label for="row">Remove</label>
                                                        <button type="button" class="remove_row form-control btn btn-info"><i class="fa fa-trash"></i></button>
                                                    </td>
                                                    <td>
                                                    </td>
                                                </tr>


                                                </tbody></table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                                            <input class="btn btn-primary" type="submit" value="Save">
                                        </div>
                                    </form>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">.2..</div>
</div>

<style>
    .details-table td{
        border: 1px solid black;
    }
</style>

<script>

    $(document).ready(function () {
      $('#add-new').on('click', function () {
        $('#multi-variations').append('<tr>'+$('#duplicate-variation').html()+'</tr>');
        /*$('#multi-variations').append('<br>');*/
      })
    })

</script>