
<div id="denim-fields">

    <div class="form-group" id="inseam-div">
        <label for="inseam" class="control-label">Inseam</label>
        <input class="form-control" placeholder="Inseam" data-counter="120" name="inseam" type="text" id="inseam" value="{{$options['data']['model']->inseam}}">
    </div>

    <div class="form-group" id="fit-div">
        <label for="fit_id" class="control-label">Select Fit</label>
        <div class="form-group">
            <select class="select-search-full" id="fit_id" name="fit_id">
                <option selected="selected" disabled value="">Select Fit</option>
                @foreach($options['data']['fits'] as $key => $fit)
                    <option value="{{$key}}" {{$options['data']['model']->fit_id == $key ? 'selected' : ''}}>{{$fit}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group" id="rise-div">
        <label for="rise_id" class="control-label">Select Rise</label>
        <div class="form-group">
            <select class="select-search-full" id="rise_id" name="rise_id">
                <option selected="selected" disabled value="">Select Rise</option>
                @foreach($options['data']['rises'] as $key => $rise)
                    <option value="{{$key}}" {{$options['data']['model']->rise_id == $key ? 'selected' : ''}}>{{$rise}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group" id="fabric-div">
        <label for="fabric_id" class="control-label">Select Fabric</label>
        <div class="form-group">
            <select class="select-search-full" id="fabric_id" name="fabric_id">
                <option selected="selected" disabled value="">Select Fabric</option>
                @foreach($options['data']['fabrics'] as $key => $fabric)
                    <option value="{{$key}}" {{$options['data']['model']->fabric_id == $key ? 'selected' : ''}}>{{$fabric}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group" id="fabric_print_direction-div">
        <label for="fabric_print_direction" class="control-label">Fabric Print Direction</label>
        <input class="form-control" placeholder="Fabric Print Direction" data-counter="120" name="fabric_print_direction" type="text" id="fabric_print_direction" value="{{$options['data']['model']->fabric_print_direction}}">
    </div>

    <div class="form-group">
        <label for="reg_pack_qty" class="control-label">Reg Pack Qty</label>
        <input class="form-control" placeholder="Reg Pack Qty" name="reg_pack_qty" type="number" id="reg_pack_qty" value="{{$options['data']['model']->reg_pack_qty}}">
    </div>

    <div class="form-group">
        <label for="plus_pack_qty" class="control-label">Plus Pack Qty</label>
        <input class="form-control" placeholder="Plus Pack Qty" name="plus_pack_qty" type="number" id="plus_pack_qty" value="{{$options['data']['model']->plus_pack_qty}}">
    </div>

</div>

<div class="form-group">
    <label for="spec_file" class="control-label">Tech Spec Files <span class="image-sugg">Image Size (270px by 170px)</span></label>
    <div class="image-box">
        <input type="file" name="spec_files[]" class="image-data" multiple>
        <br><br>
        @foreach($options['data']['model']->spec_files as $spec_file)
            <div class="preview-image-wrapper">
                <img src="{{url($spec_file->spec_file)}}" alt="Preview image" class="preview_image" width="150">
                <a href="javascript:void(0)" class="rem-spec-file" data-value="{{$spec_file->id}}" title="Remove image">
                    <i class="fa fa-times"></i>
                </a>
            </div>
        @endforeach
    </div>
</div>

<script>
  $(document).ready(function () {
      setTimeout(() => {
          $('#is_denim').trigger('change');
      },300);

      $('#is_denim').change(function() {
          if (this.checked) {
              $('div#denim-fields').removeClass('hidden');
              $('label.material-ip').parent().parent().parent().hide();
          } else {
              $('div#denim-fields').addClass('hidden');
              $('label.material-ip').parent().parent().parent().show();
          }
      });

      $('select#pp_sample').change(function() {
          var pp_sample_date = new Date($('#order_date').val());
          pp_sample_date.setDate(pp_sample_date.getDate() + 50);
          if ($(this).val() == 'yes') {
              $('#pp_sample_date').val(pp_sample_date).trigger('change');
              // $('#pp_sample_date').datepicker('setDate', pp_sample_date);
          } else {
              $('#pp_sample_date').val('');
          }
      });

      $(document).on('click', 'a.rem-spec-file', function () {
          let $this = $(this);
          let specFileId = $(this).data('value');
          $.ajax({
              url : '/admin/threads/removeThreadSpecFile/'+specFileId,
              type : 'GET',
              success : function(data) {
                  $this.parent().remove();
              },
              error : function(request,error) {}
          });
      });

  });
</script>

<style></style>
