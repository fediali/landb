<?php

$thread = $options['data']['thread'];
$variations = $options['data']['variations'];

?>
<div class="row">
    <div class="col-md-12">
        <hr>
        <div class="box-body">
            <a href="javascript:void(0)" class="btn btn-secondary float-right" data-toggle="modal" data-target="#add_variation"> Add Variation</a><br>
            <table class="table" id="thread-variations">
                <thead>
                <tr>
                    <th scope="col">Thread</th>
                    <th scope="col">Fabric or Print</th>
                    <th scope="col">Cost</th>
                    <th scope="col">Name</th>
                    <th scope="col">Sku</th>
                    <th scope="col">Status</th>
                    <th scope="col">Regular Qty</th>
                    <th scope="col">Plus Qty</th>
                    <th scope="col">Notes</th>
                    <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($variations as $key => $variation)
                    <tr>
                        <td>{{ $thread->name }}</td>
                        <td>{{ @$variation->printdesign->name }}</td>
                        <td>{{ $variation->cost }}</td>
                        <td>{{ $variation->name }}</td>
                        <td>{{ $variation->sku }}</td>
                        <td>
                            @if($variation->status == 'active')
                                <a href="{{ route('thread.updateVariationStatus', ['id' => $variation->id, 'status' => 'inactive']) }}" class="btn btn-success">{{ ucfirst($variation->status) }}</a>
                            @elseif($variation->status == 'inactive')
                                <a href="{{ route('thread.updateVariationStatus', ['id' => $variation->id, 'status' => 'active']) }}" class="btn btn-danger">{{ ucfirst($variation->status) }}</a>
                            @endif
                        </td>
                        <td>{{ $variation->regular_qty }}</td>
                        <td>{{ $variation->plus_qty }}</td>
                        <td>{{ $variation->notes }}</td>
                        <td>
                            <div class="table-actions" style="display: inline-block; font-size: 5px">
                               {{-- <a class="btn btn-success btn-sm"><i class="fa fa-eye"></i></a>
                                <a class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                                <a class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>--}}
                            </div>
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="modal fade in" id="modal-default" style="display: none; padding-right: 17px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Add More Fabric to <span class="variation-name"></span></h4>
            </div>
            <form method="post" action="{{ route('thread.addVariationPrints') }}">
                @csrf
                <div class="modal-body">
                    <p>
                        <label for="name">Variation Name:</label>
                        <input class="form-control" placeholder="Enter Variation Name" name="name" type="text" id="name">
                        <label for="print_id">Print / Solid:</label>
                        <select class="form-control" id="print_id" name="print_id">
                            <option selected="selected" value="">Enter Print</option>
                            @foreach($options['data']['printdesigns'] as $key => $print)
                                <option value="{{ $key }}">{{ $print }}</option>
                            @endforeach
                        </select>
                        <input class="thread_variation_id" name="thread_variation_id" type="hidden">
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
                <input name="thread_id" type="hidden" value="{{ $thread->id }}">
                <div class="modal-body">
                    <table class="table table-striped table-bordered">

                        <tbody id="multi-variations">
                        <tr>
                            <td style="width: 13%;">
                                <button type="button" class="add_row form-control btn btn-secondary" id="add-new"><i class="fa fa-plus"></i> Add:</button>
                            </td>
                        </tr>
                        <tr class="duplicate">
                            <td width="10%">
                                <label for="name">Name:</label>
                                <input required class="form-control" placeholder="Add Name" name="name[]" type="text">
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
                                <input required class="form-control" placeholder="Add Regular Quantity" name="regular_qty[]" type="text">
                            </td>
                            <td width="10%">
                                <label for="pack_id">Plus:</label>
                                <input required class="form-control" placeholder="Add Plus Quantity" name="plus_qty[]" type="text">
                            </td>
                            <td width="15%">
                                <label for="cost">Cost:</label>
                                <input required class="form-control" placeholder="Add Cost" name="cost[]" type="text">
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

<script>
    $(document).ready(function () {
      $(document).on('click', '.remove_row', function (e) {
        var count = $('.duplicate').length;
        console.log(count);
        if (count !== 1) {
          $tr = $(this).closest("tr");
          $tr.remove();
          e.preventDefault();
        }
      });

      $("#add-new").on("click", function () {
        $tr = $(this).closest("tr").next().clone();
        $tr.insertAfter($(this).closest("tr"));
      });

    });
</script>
