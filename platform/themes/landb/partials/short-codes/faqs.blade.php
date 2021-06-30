<section class="shoplisting_wrap pl-5 pr-5 mbtb-pl-1 mbtb-pr-1">
    <div class="row">
        <div class="col-lg-12 accordion_one">
            <div class="panel-group" id="accordion">
                @foreach($faqs = \Botble\Faq\Models\Faq::where('status','published')->get() as $key => $faq)
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{$key}}">{{ $faq->question }}</a>
                        </h4>
                    </div>
                    <div id="collapse{{$key}}" class="panel-collapse collapse in">
                        <div class="panel-body">{{ $faq->answer }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>