
<div id="denim-fields">

    <div class="form-group" id="inseam-div">
        <label for="inseam" class="control-label">Inseam</label>
        <input class="form-control" placeholder="Inseam" data-counter="120" name="inseam" type="text" id="inseam">
    </div>

    <div class="form-group" id="fit-div">
        <label for="fit_id" class="control-label">Select Fit</label>
        <div class="form-group">
            <select class="select-search-full" id="fit_id" name="fit_id">
                <option selected="selected" value="">Select Fit</option>
                @foreach($options['data']['fits'] as $key => $fit)
                    <option value="{{$key}}">{{$fit}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group" id="rise-div">
        <label for="rise_id" class="control-label">Select Rise</label>
        <div class="form-group">
            <select class="select-search-full" id="rise_id" name="rise_id">
                <option selected="selected" value="">Select Rise</option>
                @foreach($options['data']['rises'] as $key => $rise)
                    <option value="{{$key}}">{{$rise}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group" id="fabric-div">
        <label for="fabric_id" class="control-label">Select Fabric</label>
        <div class="form-group">
            <select class="select-search-full" id="fabric_id" name="fabric_id">
                <option selected="selected" value="">Select Fabric</option>
                @foreach($options['data']['fabrics'] as $key => $fabric)
                    <option value="{{$key}}">{{$fabric}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group" id="fabric_print_direction-div">
        <label for="fabric_print_direction" class="control-label">Fabric Print Direction</label>
        <input class="form-control" placeholder="Fabric Print Direction" data-counter="120" name="fabric_print_direction" type="text" id="fabric_print_direction">
    </div>

    <div class="form-group" id="Wash-div">
        <label for="wash" class="control-label">Wash</label>
        <input class="form-control" placeholder="Wash" data-counter="120" name="wash" type="text" id="wash">
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
          } else {
              $('div#denim-fields').addClass('hidden');
          }
      });
  });
</script>


<style>

</style>
