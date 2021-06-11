<section class="latest_collection">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="collectiontext">
                    <div class="small_h">
                        Latest Collection
                    </div>
                    <h1 class="revealUp">Content <span></span> <br>
                        <small>Come</small> Here Lorem ipsum </h1>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type
                        specimen book. It has survived not only five centuries.</p>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type
                        specimen book. It has survived not only five centuries.</p>

                    <a href="{{ URL::to('products') }}" class="lookbook_btn">LookBook</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="collection_img">
                    <div class="collec-imgbox">
                        {!! image_html_generator(@$latest_collection[0]->images[0]) !!}
                        <div class="imgcaption">
                            <a href="{!! generate_product_url('save', @$latest_collection[0]->id) !!}" class="save"><i class="fas fa-save"></i> Save</a>
                            <a href="{!! generate_product_url('detail', @$latest_collection[0]->id, @$latest_collection[0]->product_slug) !!}" class="search"><i class="fal fa-search"></i></a>
                        </div>
                    </div>
                    @if(isset($latest_collection[1]))
                    <div class="overlap">
                        <div class="collec-imgbox">
                            {!! image_html_generator(@$latest_collection[1]->images[0], @$latest_collection[1]->name, null, null, true, 'imgtop') !!}
                            <div class="imgcaption">
                                <a href="{!! generate_product_url('save', @$latest_collection[1]->id) !!}" class="save"><i class="fas fa-save"></i> Save</a>
                                <a href="{!! generate_product_url('detail', @$latest_collection[1]->id, @$latest_collection[1]->product_slug) !!}" class="search"><i class="fal fa-search"></i></a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>