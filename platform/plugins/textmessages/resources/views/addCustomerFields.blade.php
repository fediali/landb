
<div id="customer-fields">
    <div class="widget meta-boxes" id="cust-range-div">
        <div class="widget-title">
            <h4><label for="customer_range" class="control-label">Select Range</label></h4>
        </div>
        <div class="widget-body">
            <div class="ui-select-wrapper form-group">
                <select class="form-control select-full ui-select ui-select select2-hidden-accessible" id="customer_range" name="customer_range">
                    <option selected="selected" value="">Select Range</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="500">500</option>
                    <option value="1000">1000</option>
                    <option value="2000">2000</option>
                    <option value="3000">3000</option>
                    <option value="4000">4000</option>
                    <option value="5000">5000</option>
                    <option value="10000">10000</option>
                </select>
            </div>
        </div>
    </div>

    <div class="widget meta-boxes" id="man-ph-div">
        <div class="widget-title">
            <h4><label for="manual_phone" class="control-label">Enter Phone</label></h4>
        </div>
        <div class="widget-body">
            <input class="form-control" placeholder="Enter Phone" name="manual_phone" type="text" id="manual_phone">
        </div>
    </div>
</div>

<script>
  $(document).ready(function () {
      setTimeout(() => {
          $('#customer_type').trigger('change');
      },300);

      $('#customer_type').change(function() {
          if ($(this).val() == 'auto') {
              $('div#cust-range-div').removeClass('hidden');
              $('div#man-ph-div').addClass('hidden');
          } else {
              $('div#cust-range-div').addClass('hidden');
              $('div#man-ph-div').removeClass('hidden');
          }
      });
  });
</script>



