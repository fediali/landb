<!DOCTYPE html>
<html lang="en-US">

<head>
    <style>
        @font-face {
            font-family: 'Saol';
            src: url('./SaolDisplay-Regular.ttf');
        }
        body {
            font-family: 'arial' !important;
        }
    </style>
</head>

<body style="background:#ddd;">
<div style="background-color: #fffdfc; width: 500px; margin: auto">
    <img style="text-align:center; display: block; margin: auto; height: 70px; padding-top: 20px;"
         src="https://landbapparel.com/images/logos/174/logo_lnb_new.png"/>
    <hr>
    <h3 style=" text-align: center;">
        Your Order Product has Shipped!
    </h3>
    <div style="background:#ddd; padding:20px; margin:20px; border-radius:5px;">
        @foreach($products as $product)
            <a href="#" style="display:flex; text-decoration:none;">
                <div style="width:20%;margin: auto;">
                    @php
                        $getProdImage = Illuminate\Support\Facades\DB::connection('mysql2')->table('hw_images_links')
                            ->select('hw_images.image_id', 'hw_images.image_path', 'hw_images_links.type')
                            ->join('hw_images', 'hw_images.image_id', 'hw_images_links.detailed_id')
                            ->where('hw_images_links.object_type', 'product')
                            ->where('hw_images_links.object_id', $product->product_id)
                            ->orderBy('hw_images_links.type', 'DESC')
                            ->first();
                        $idLen = getDigitsLength($getProdImage->image_id);
                        if ($idLen <= 5) {
                            $folder = substr($getProdImage->image_id, 0, 2);
                        } elseif ($idLen >= 6) {
                            $folder = substr($getProdImage->image_id, 0, 3);
                        }
                    @endphp
                    <img style="height: 60px;" src="https://landbapparel.com/images/thumbnails/278/417/detailed/{{$folder}}/{{$getProdImage->image_path}}"/>
                </div>
                <div style="width:80%;margin: auto;">
                    <p style="color:#000;">{{$product->product_name}}</p>
                    <p style="color:#000;"><b>$ {{$product->price}}</b></p>
                </div>
            </a>
            <hr>
        @endforeach
    </div>

    <p style="text-align:center; margin-top:50px;">
        <b>Do you need with help your order?</b>
    </p>
    <p style="text-align:center; margin-top:20px; font-weight: 600; padding-bottom:20px;">
        Call Now: <a href="tel:972-243-7860" target="_blank" style="
    color: #000;
    text-decoration: none;
">972-243-7860</a>
    </p>
    <p style="
    text-align: center;
    padding-bottom: 25px;
">

    <a href="https://www.linkedin.com/company/luckyandblessed/" target="_blank" rel="nofollow external"><img src="https://landbw.co/images/companies/linkedin.png" width="30" height="30" alt="Follow L&amp;B on LinkedIn"></a>
    <a href="https://twitter.com/landb_official/" target="_blank" rel="nofollow external"><img src="https://landbw.co/images/companies/twitter.png" width="30" height="30" alt="Follow L&amp;B on Twitter"></a>
    <a href="https://www.facebook.com/LuckyandBlessedOfficial/" target="_blank" rel="nofollow external"><img src="https://landbw.co/images/companies/facebook.png" width="30" height="30" alt="Follow L&amp;B on Facebook"></a>
    <a href="https://www.instagram.com/luckyandblessed_official/" target="_blank" rel="nofollow external"><img src="https://landbw.co/images/companies/instagram.png" width="30" height="30" alt="Follow L&amp;B on Instagram"></a>
    <a href="https://www.pinterest.com/landb_official/" target="_blank" rel="nofollow external"><img src="https://landbw.co/images/companies/pinterest.png" width="30" height="30" alt="Follow L&amp;B on Pinterest"></a>
    <a href="https://www.tiktok.com/@luckyandblessed_official" target="_blank" rel="nofollow external"><img src="https://revamp.landbw.co/storage/tiktok.png" width="30" height="30" alt="Follow L&amp;B on Tiktok"></a>
</p>

<p style="
    text-align: center;
    padding-bottom: 20px;
    font-weight: 600;
    padding-left: 30px;
    padding-right: 30px;
">
    Copyright © 2022 Lucky &amp; Blessed, All rights reserved.<br>2309 Springlake Rd Suite 650, Farmers Branch, TX 75234
</p>

</div>
</body>

</html>
