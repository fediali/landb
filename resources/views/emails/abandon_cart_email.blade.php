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
         src="https://landbapparel.com/images/logos/174/lucky_blessed_logo_BLK.png"/>
    <hr>
    <h3 style=" text-align: center;">
        Your Cart made us send this reminder.
    </h3>
    <p style="text-align:center;">
        You have made a great products selection. It still in your cart.
    </p>
    <div style="background:#ddd; padding:20px; margin:20px; border-radius:5px;">
        @foreach($sessions as $session)
            <a href="#" style="display:flex; text-decoration:none;">
                <div style="width:15%;margin: auto;">
                    @php
                        $getProdImage = Illuminate\Support\Facades\DB::connection('mysql2')->table('hw_images_links')
                            ->select('hw_images.image_id', 'hw_images.image_path', 'hw_images_links.type')
                            ->join('hw_images', 'hw_images.image_id', 'hw_images_links.detailed_id')
                            ->where('hw_images_links.object_type', 'product')
                            ->where('hw_images_links.object_id', $session->product_id)
                            ->orderBy('hw_images_links.type', 'DESC')
                            ->first();
                        $idLen = getDigitsLength($getProdImage->image_id);
                        if ($idLen <= 5) {
                            $folder = substr($getProdImage->image_id, 0, 2);
                        } elseif ($idLen >= 6) {
                            $folder = substr($getProdImage->image_id, 0, 3);
                        }
                    @endphp
                    <img style="height: 60px;"
                         src="https://landbapparel.com/images/thumbnails/278/417/detailed/{{$folder}}/{{$getProdImage->image_path}}"/>
                </div>
                <div style="width:85%;margin: auto;">
                    <p style="color:#000;">{{$session->product_name}}</p>
                    <p style="color:#000;"><b>$ {{$session->price}}</b></p>
                </div>
            </a>
            <hr>
        @endforeach
    </div>

    <a href="https://landbapparel.com" style="
          background-color: #000;
          color: #fff;
		  font-size:16px;
          padding: 15px 45px;
          margin: auto;
           text-align: center;
          border: none;
          margin-top: 40px !important;
          display: block;
          margin-bottom: 40px;
        ">
        Continue Shopping
    </a>

    <p style="text-align:center; margin-top:50px;">
        <b>Do you need help to complete you order?</b>
    </p>
    <p style="text-align:center; margin-top:20px; padding-bottom:20px;">
        Call Now: <a href="tel:972-243-7860">972-243-7860</a>
    </p>

</div>
</body>

</html>
