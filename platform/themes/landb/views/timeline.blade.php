<section class="breadcrumb_wrap">
    <div class="pl-5 pr-5 mbtb-pl-1 mbtb-pr-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active" aria-current="page"><b>Styles of The Day</b></li>
            </ol>
        </nav>
    </div>
</section>
<section>
    {{--    <div class="row m-0">--}}
    {{--        <div class="col-lg-10"></div>--}}
    {{--        <div class="col-lg-2">--}}
    {{--            <button onclick="closeOnSelectDemo()" class="btn btn-calender"> Select Date</button>--}}
    {{--        </div>--}}
    {{--    </div>--}}
    <div class="timeline">
        @isset($product)
            @foreach($product->detail as $row)
                <div class="container-timeline left {{($loop->iteration % 2 == 0) ? 'right': 'left'}}">
                    <div class="date">
                        {{--                        {{$product->date}}--}}
                        <a href="https://landbapparel.com/deal-of-the-day/" class=" btn cart-btn ml-2 timeline-addbtn">View the style</a>
                        <a href="tel:972-243-7860" class="timeline-callbtn btn border-btn ml-2">Call Us</a>
                    </div>
                    <div class="content">
                        <img class="w-100" src="{{url('storage/'.$row->product_image)}}"/>
                    </div>
                </div>
            @endforeach
        @endisset
    </div>
</section>
<script>
    var datepicker = new Datepickk();
</script>
<script>
    function closeOnSelectDemo() {
        datepicker.unselectAll();
        datepicker.closeOnSelect = true;
        console.log(datepicker.closeOnSelect);
        datepicker.onClose = function () {
            datepicker.closeOnSelect = false;
            datepicker.onClose = null;
        }
        datepicker.show();
    }
</script>


