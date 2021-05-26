
<div id="pvt-label-fields" class="hidden">

    <div class="widget meta-boxes">
        <div class="widget-title">
            <h4><label for="is_pieces" class="control-label">Qty is in Pieces ?</label></h4>
        </div>
        <div class="widget-body">
            <input class="hrv-checkbox" id="is_pieces" name="is_pieces" type="checkbox" value="{{$options['data']['model']->is_pieces ? $options['data']['model']->is_pieces : 0}}" {{$options['data']['model']->is_pieces ? 'checked' : ''}}>
        </div>
    </div>

    <div class="widget meta-boxes">
        <div class="widget-title">
            <h4><label for="pvt_customer_id" class="control-label">Select Private Customer</label></h4>
        </div>
        <div class="widget-body">
            <div class="ui-select-wrapper form-group">
                <select class="select-search-full" id="pvt_customer_id" name="pvt_customer_id">
                    <option selected="selected" disabled value="">Select Private Customer</option>
                    @foreach($options['data']['private_customers'] as $key => $customer)
                        <option value="{{$key}}" {{$options['data']['model']->pvt_customer_id == $key ? 'selected' : ''}}>{{$customer}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

</div>



<script>
  $(document).ready(function () {

      setTimeout(() => {
          $('select#sel-thread-status').trigger('change');
      },200);

      $('select#sel-thread-status').change(function() {
          if ($(this).val() == "{{\Botble\Thread\Models\Thread::PRIVATE}}") {
              $('div#pvt-label-fields').removeClass('hidden');
              $('label.pvt-ip').parent().parent().parent().show();
          } else {
              $('div#pvt-label-fields').addClass('hidden');
              $('label.pvt-ip').parent().parent().parent().hide();
          }
      });

  });
</script>

<style></style>
