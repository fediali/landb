<div class="wrapper-filter">
    <p>Calender</p>
    <form id="acc-sys-form">
        <div class="filter_list inline-block filter-items-wrap">
            <div class="filter-item form-filter filter-item-default">
                <input type="date" name="sel_date" class="form-control" id="sel-date" value="{{request('sel_date', date('Y-m-d'))}}">
            </div>
        </div>
    </form>
</div>


<br>
<br>
<div class="wrapper-filter">
    <div class="col-md-2"><strong>Date : {{date('M d, Y')}}</strong></div>
    <div class="col-md-8">
        <strong>Total Cash : $ {{$data['totalCash']}}</strong> ||
        <strong>Leftover : $ {{$data['leftover']}}</strong> ||
        <strong>Difference : $ {{$data['diff']}}</strong>
    </div>
    <div class="col-md-2"><strong>Time : {{date('h:i a')}}</strong></div>
</div>

<script>
    $(document).ready(function () {
        $("#sel-date").change(function() {
            this.form.submit();
        });
    });
</script>
