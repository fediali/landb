<section class="breadcrumb_wrap">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active" aria-current="page">Shop</li>
            </ol>
        </nav>
    </div>
</section>
<section class="shoplisting_wrap">
    <div class="container">
        <div class="filterbar d_flex">
            <ul class="leftbar">
                <li>Showing 1-12 of 21 results</li>
                <li class="seprator"></li>
                <li>
                    <div class="dropdown">
                        <button class="sortdropdown" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Sort by <i class="fal fa-angle-down"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#">Type</a>
                            <a class="dropdown-item" href="#">Name</a>
                        </div>
                    </div>
                </li>
            </ul>
            <ul class="rightbar">
                <li class="listingicon">
                    <a href="#"><span class="threedots"></span></a>
                </li>
                <li class="listingicon">
                    <a href="#"><span class="fourdots active"></span></a>
                </li>
                <li class="listingicon">
                    <a href="#"><span class="fivedots"></span></a>
                </li>
                <li class="seprator"></li>
                <li class="filter"><a href="#" id="filtertoggle" class="filterbtn">Filter <span class="filtericon"></span></a>


                </li>
            </ul>
        </div>
        <div class="" id="filtermenu">
            <nav class="main-nav filter_nav">
                <div class="nav-col">
                    <h5>Color</h5>
                    <ul class="colorbox">
                        <li>
                            <a class="black" href="#">
                                Black
                            </a>
                        </li>
                        <li>
                            <a class="green" href="#">
                                Green
                            </a>
                        </li>

                        <li>
                            <a class="gray" href="#">
                                Grey
                            </a>
                        </li>
                        <li>
                            <a class="red" href="#">
                                Red
                            </a>
                        </li>
                        <li>
                            <a class="white" href="#">
                                White
                            </a>
                        </li>
                        <li>
                            <a class="yellow" href="#">
                                Yellow
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="nav-col">
                    <h5>Size</h5>
                    <ul>
                        <li>
                            <a class="selected" href="#">
                                Large
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                Medium
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                Small
                            </a>
                        </li>

                    </ul>
                </div>
                <div class="nav-col">
                    <h5>Price</h5>
                    <ul>
                        <li>
                            <a class="selected" href="#">
                                $0.00 - $20.00
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                $20.00 - $40.00
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                $40.00 - $50.00
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                $50.00 - $60.00
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                $60.00 +
                            </a>
                        </li>



                    </ul>
                </div>
                <div class="nav-col">
                    <h5>Categories</h5>
                    <ul>
                        <li>
                            <a href="#">
                                ALL
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                Men
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                Women
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                Kids
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                Accessories
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                Boot
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="nav-col">
                    <h5>Tags</h5>
                    <ul class="tags">
                        <li>
                            <a href="#">
                                All Asian,
                            </a>
                            <a href="#">
                                Brown,

                            </a>
                            <a href="#">
                                euro,

                            </a>
                            <a href="#">
                                hat,

                            </a>
                            <a href="#">
                                T-Shirt,


                            </a>
                            <a href="#">
                                Teen,
                            </a>
                            <a href="#">
                                Top,

                            </a>
                            <a href="#">
                                Pants
                            </a>



                        </li>




                    </ul>
                </div>
            </nav>


        </div>
        <div class="shoplisting">
            @foreach($products as $key => $product)
                <div class="listbox">
                <div class="img">
                    {!! image_html_generator(@$product->images[0]) !!}
                    <div class="caro_text">
                        <h5>Dresses</h5>
                        <p>Sandon vegan Leatther Dress</p>
                    </div>
                    <span>Restock</span>
                    <div class="imgoverlay">
                        <a href="{!! generate_product_url('detail', $product->id) !!}"><i class="far fa-eye"></i></a>
                        <a class="add-to-wishlist" href="{!! generate_product_url('wishlist', $product->id) !!}"><i class="far fa-heart"></i></a>
                        <a href="{!! generate_product_url('cart', $product->id) !!}"><i class="far fa-shopping-bag"></i></a>
                    </div>
                </div>
                <div class="caption">
                    <h4>{{ $product->name }}</h4>
                    <div class="price">
                        ${{ $product->price }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        {!! $products->links() !!}
       {{-- <div class="pagination">

            <ul>
                <li>
                    <a href="#" class="prev"> <i class="fal fa-long-arrow-left"></i> Prev</a>
                </li>
                <li>
                    <a href="#" class="">1</a>
                </li>
                <li>
                    <a href="#" class="">2</a>
                </li>
                <li>
                    <a href="#" class="">3</a>
                </li>
                <li>
                    <a href="#" class="">4</a>
                </li>
                <li>
                    <a href="#" class="">5</a>
                </li>
                <li>
                    <a href="#" class="">6</a>
                </li>
                <li>
                    <a href="#" class="next">Next <i class="fal fa-long-arrow-right"></i></a>
                </li>
            </ul>
            <ul>
                <li>2-16</li>
            </ul>
        </div>--}}
    </div>
</section>