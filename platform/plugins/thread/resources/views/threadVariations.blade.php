<?php

$thread = $options['data']['thread'];
$variations = $options['data']['variations'];

?>
<div class="row">
    <div class="col-md-12">
        <hr>
        @if($thread->is_denim == 1)
        <div class="box-body">
            <a href="javascript:void(0)" class="btn btn-secondary float-right" data-toggle="modal" data-target="#add_variation"> Add Variation</a><br>
            <table class="table" id="thread-variations">
                <thead>
                <tr>
                    <th scope="col">Thread</th>
                    <th scope="col">Fabric or Print</th>
                    <th scope="col">Wash</th>
                    <th scope="col">Status</th>
                    <th scope="col">Notes</th>
                    <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($variations as $key => $variation)
                    @if($variation->is_denim == 1)
                    <tr>
                        <td>{{ $thread->name }}</td>
                        <td>{{ @$variation->printdesign->name }}</td>
                        <td>{{ @$variation->wash->name  }}</td>
                        <td>
                            @if($variation->status == 'active')
                                <a href="{{ route('thread.updateVariationStatus', ['id' => $variation->id, 'status' => 'inactive']) }}" class="btn btn-success">{{ ucfirst($variation->status) }}</a>
                            @elseif($variation->status == 'inactive')
                                <a href="{{ route('thread.updateVariationStatus', ['id' => $variation->id, 'status' => 'active']) }}" class="btn btn-danger">{{ ucfirst($variation->status) }}</a>
                            @endif
                        </td>
                        <td>{{ $variation->notes }}</td>
                        <td>
                            <div class="table-actions" style="display: inline-block; font-size: 5px">
                                {{--<a class="btn btn-success btn-sm"><i class="fa fa-eye"></i></a>
                                <a class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>--}}
                                <a href="{{ route('thread.removeVariation', ['id'=> $variation->id]) }}" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    @endif
                @endforeach

                </tbody>
            </table>
        </div>
        <div class="modal fade in" id="add_variation" style="display: none; padding-right: 17px;">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                            <h4 class="modal-title">Adding Variations<span class="variation-name"></span></h4>
                        </div>
                        {{--<form method="POST" action="{{ URL::to('/admin/threads/add-variations') }}" accept-charset="UTF-8" enctype="multipart/form-data">
                            @csrf--}}
                            <input name="variation_thread_id" type="hidden" value="{{ $thread->id }}">
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
                                            <input required class="form-control variation_name" placeholder="Add Name" name="name[]" type="text">
                                        </td>

                                        <td width="15%">
                                            <label for="print_id">Print / Solid:</label>
                                            {{--<select hidden class="form-control select variation_print" name="print_id[]">
                                                <option selected="selected" value="">Select Print</option>
                                                @foreach($options['data']['printdesigns'] as $key => $print)
                                                    <option value="{{ $print->id }}">{{ $print->name }}</option>
                                                @endforeach
                                            </select>--}}
                                            <input type="hidden" class="print_id" name="print_id[]">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Select Print
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    @foreach($options['data']['printdesigns'] as $key => $print)
                                                        <a class="dropdown-item print_id_drop" href="#" data-id="{{ $print->id }}"><img class="" src="{{ asset('storage/'.$print->file) }}" height="40" width="40" alt="Image" title="Image Not Available"  /> {{ $print->name }}</a>
                                                    @endforeach

                                                </div>
                                            </div>
                                        </td>
                                        <td width="15%">
                                            <label for="wash_id" class="control-label">Select Wash</label>
                                                <select class="select-search-full variation_wash" id="wash_id" name="wash_id[]">
                                                    <option selected="selected" disabled value="">Select Wash</option>
                                                    @foreach($options['data']['washes'] as $key => $wash)
                                                        <option value="{{$key}}" >{{$wash}}</option>
                                                    @endforeach
                                                </select>
                                        </td>
                                        {{--<td width="10%">
                                            <label for="pack_id">File:</label>
                                            <input required class="form-control variation_file" placeholder="Add File" name="file[]" type="file">
                                        </td>--}}
                                        <td width="15%">
                                            <label for="Notes">Notes:</label>
                                            <textarea class="form-control variation_notes" placeholder="Add Notes" name="notes[]" cols="50" rows="2"></textarea>
                                        </td>
                                        <td width="10%">

                                            <label for="row">Remove</label>
                                            <button type="button" class="remove_row form-control btn btn-info"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>


                                    </tbody></table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                                <input class="btn btn-primary" id="submit_denim_variation" value="Save">
                            </div>
                        {{--</form>--}}
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
        @else
        <div class="box-body">
                <a href="javascript:void(0)" class="btn btn-secondary float-right" data-toggle="modal" data-target="#add_variation"> Add Variation</a><br>
                <table class="table" id="thread-variations">
                    <thead>
                    <tr>
                        <th scope="col">Thread</th>
                        <th scope="col">Fabric or Print</th>
                        <th scope="col">Cost</th>
                        <th scope="col">Name</th>
                        <th scope="col">Regular Sku</th>
                        <th scope="col">Plus Sku</th>
                        <th scope="col">Status</th>
                        <th scope="col">Regular Qty</th>
                        <th scope="col">Plus Qty</th>
                        <th scope="col">Notes</th>
                        <th scope="col">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($variations as $key => $variation)
                        @if($variation->is_denim == 0)
                        <tr>
                            <td>{{ $thread->name }}</td>
                            <td>{{ @$variation->printdesign->name }}</td>
                            <td>{{ $variation->cost }}</td>
                            <td>{{ $variation->name }}</td>
                            <td>{{ $variation->sku }}</td>
                            <td>{{ $variation->plus_sku }}</td>
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
                                    {{--<a class="btn btn-success btn-sm"><i class="fa fa-eye"></i></a>
                                    <a class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>--}}
                                    <a href="{{ route('thread.removeVariation', ['id'=> $variation->id]) }}" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @endforeach

                    </tbody>
                </table>
            </div>
        <div class="modal fade in" id="add_variation" style="display: none; padding-right: 17px;">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                            <h4 class="modal-title">Adding Variations<span class="variation-name"></span></h4>
                        </div>
                        {{--<form method="POST" action="{{ URL::to('/admin/threads/add-variations') }}" accept-charset="UTF-8" enctype="multipart/form-data">
                            @csrf--}}
                            <input name="variation_thread_id" type="hidden" value="{{ $thread->id }}">
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
                                            <input required class="form-control variation_name" placeholder="Add Name" name="name[]" type="text">
                                        </td>

                                        <td width="15%">
                                            <label for="print_id">Print / Solid:</label>
                                            {{--<select hidden class="form-control select variation_print" name="print_id[]">
                                                <option selected="selected" value="">Select Print</option>
                                                @foreach($options['data']['printdesigns'] as $key => $print)
                                                    <option value="{{ $print->id }}">{{ $print->name }}</option>
                                                @endforeach
                                            </select>--}}
                                            <input type="hidden" class="print_id" name="print_id[]">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Select Print
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    @foreach($options['data']['printdesigns'] as $key => $print)
                                                        <a class="dropdown-item print_id_drop" href="#" data-id="{{ $print->id }}"><img class="" src="{{ asset('storage/'.$print->file) }}" height="40" width="40" alt="Image" title="Image Not Available"  /> {{ $print->name }}</a>
                                                    @endforeach

                                                </div>
                                            </div>
                                        </td>
                                        <td width="15%">
                                            <label for="pack_id">Regular:</label>
                                            <input required class="form-control variation_qty" placeholder="Add Regular Quantity" name="regular_qty[]" type="text">
                                        </td>
                                        <td width="10%">
                                            <label for="pack_id">Plus:</label>
                                            <input required class="form-control variation_plus_qty" placeholder="Add Plus Quantity" name="plus_qty[]" type="text">
                                        </td>
                                        <td width="15%">
                                            <label for="cost">Cost:</label>
                                            <input required class="form-control variation_cost" placeholder="Add Cost" name="cost[]" type="text">
                                        </td>
                                        <td width="15%">
                                            <label for="Notes">Notes:</label>
                                            <textarea class="form-control variation_notes" placeholder="Add Notes" name="notes[]" cols="50" rows="2"></textarea>
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
                                <input class="btn btn-primary" type="button" id="submit_variation" value="Save">
                            </div>
                        {{--</form>--}}
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
        @endif
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
                <div class="modal-body">
                    <p>
                        <label for="name">Variation Name:</label>
                        <input class="form-control" placeholder="Enter Variation Name" name="name" type="text" id="variation_fabic_name">
                        <label for="print_id">Print / Solid:</label>
                        <select class="form-control" id="variation_print_id" name="variation_print_id">
                            <option selected="selected" value="">Enter Print</option>
                            @foreach($options['data']['printdesigns'] as $key => $print)
                                <option value="{{ $print->id }}">{{ $print->name }}</option>
                            @endforeach
                        </select>
                        <input class="thread_variation_id" name="thread_variation_id" id="thread_variation_id" type="hidden">
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <input class="btn btn-primary" value="Save" id="submitVariationFabric">
                </div>
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

      $('.print_id_drop').on('click', function () {
        $(this).closest('tr').find('.print_id').val($(this).data('id'));
      })

      $(document).on('click', '.add_print', function () {
        var variation_id = $(this).data('id');
        $('.thread_variation_id').val(variation_id);
      });

      $(document).on('click', '#submitVariationFabric', function () {
       $.ajax({

            url : '{{ route('thread.addVariationPrints') }}',
            type : 'post',
            data : {
                'thread_variation_id' : $('#thread_variation_id').val(),
                '_token' : '{{ csrf_token() }}',
                'name' : $('#variation_fabic_name').val(),
                'print_id' : $('#variation_print_id').val(),
            },
            success : function(data) {
              location.reload();
            },
            error : function(request,error)
            {
              location.reload();
            }
        });
      });


      $(document).on('click', '#submit_variation', function () {
        $.ajax({

          url : '{{ route('thread.addVariation') }}',
          type : 'post',
          data : {
            'is_denim': 0,
            'thread_id' : $('input[name="variation_thread_id"]').val(),
            '_token' : '{{ csrf_token() }}',
            'name[]' : $('input:text.variation_name').map(function(){
              return this.value;
            }).get(),
            'print_id[]' :$('input.print_id').map(function(){
              return this.value;
            }).get(),
            'regular_qty[]' :$('input:text.variation_qty').map(function(){
              return this.value;
            }).get(),
            'plus_qty[]' :$('input:text.variation_plus_qty').map(function(){
              return this.value;
            }).get(),
            'cost[]' :$('input:text.variation_cost').map(function(){
              return this.value;
            }).get(),
            'notes[]' :$('textarea.variation_notes').map(function(){
              return this.value;
            }).get(),
          },
          success : function(data) {
            location.reload();
          },
          error : function(request,error)
          {
            location.reload();
          }
        });
      });

      $(document).on('click', '#submit_denim_variation', function () {
        $.ajax({

          url : '{{ route('thread.addVariation') }}',
          type : 'post',
          data : {
            'is_denim': 1,
            'thread_id' : $('input[name="variation_thread_id"]').val(),
            '_token' : '{{ csrf_token() }}',
            'name[]' : $('input:text.variation_name').map(function(){
              return this.value;
            }).get(),
            'print_id[]' :$('input.print_id').map(function(){
              return this.value;
            }).get(),
            'wash_id[]' :$('.variation_wash').map(function(){
              return this.value;
            }).get(),/*
            'file[]' :$('input:file.variation_file').map(function(){
              return this.value;
            }).get(),*/
            'notes[]' :$('textarea.variation_notes').map(function(){
              return this.value;
            }).get(),
          },
          success : function(data) {
            location.reload();
          },
          error : function(request,error)
          {
            location.reload();
          }
        });
      });

    });
</script>
