<section class="breadcrumb_wrap">
    <div class="pl-5 pr-5 mbtb-pl-1 mbtb-pr-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active" aria-current="page"><b>Timeline</b></li>
            </ol>
        </nav>
    </div>
</section>
<section>
    <div class="row m-0">
        <div class="col-lg-10"></div>
        <div class="col-lg-2">
            <button onclick="closeOnSelectDemo()" class="btn btn-calender"> Select Date</button>
        </div>
    </div>
    <div class="timeline">
        @foreach($product->detail as $row)
            <div class="container-timeline left">
                <div class="date"> {{$product->date}}
                    <a href="#" class=" btn cart-btn ml-2 timeline-addbtn">Add to Cart</a>
                    <a href="#" class="timeline-callbtn btn border-btn ml-2">Call Us</a>
                </div>

                <i class="icon fa fa-home"></i>
                <div class="content">
                    <img class="w-100" src="https://revamp.landbw.co/public/landb/img/browse-img-3.png"/>

                    <h2>Lorem ipsum dolor sit amet</h2>
                    <p>
                        Lorem ipsum dolor sit amet elit. Aliquam odio dolor, id luctus erat sagittis non. Ut blandit
                        semper
                        pretium.
                    </p>
                    <a href="#" class=" btn cart-btn mt-3">Check Now</a>
                </div>
            </div>
            <div class="container-timeline right">
                <div class="date">
                    <span class="d-none mobile-date">22 Oct</span>
                    <a href="#" class=" btn cart-btn mr-2 timeline-addbtn">Add to Cart</a>
                    <span class="mobile-display-none">22 Oct</span>
                    <a href="#" class="timeline-callbtn btn border-btn mr-2">Call Us</a>


                </div>
                <i class="icon fa fa-gift"></i>
                <div class="content">
                    <img class="w-100" src="https://revamp.landbw.co/public/landb/img/browse-img-3.png"/>

                    <h2>Lorem ipsum dolor sit amet</h2>
                    <p>
                        Lorem ipsum dolor sit amet elit. Aliquam odio dolor, id luctus erat sagittis non. Ut blandit
                        semper
                        pretium.
                    </p>
                    <a href="#" class=" btn cart-btn mt-3">Check Now</a>
                </div>
            </div>
        @endforeach
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


