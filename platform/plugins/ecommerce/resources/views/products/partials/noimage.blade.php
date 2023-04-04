<a href="javascript:void(0)" class="noImg" id="prod-img-{{$item->id}}"><img src="https://revamp.landbw.co/storage/img.jpg"/></a>
<img class="product-gif " style="display: none;" src="https://revamp.landbw.co/storage/double-ring-1s-200px.gif"/>
<a class="show-image-{{$item->id}}" style="display: none;" href="{{ route('products.edit', $item->id) }}" title="{{ $item->name }}"></a>

<script>
    $(document).ready(function () {
        $("#prod-img-{{$item->id}}").click(function () {
            let context = this;
            $(context).parent().find('.product-gif').show();
            $(context).parent().find('.noImg').hide();
            $.ajax({
                url: '{{ route('products.loadProductImage', $item->id) }}',
                success: function (data) {
                    $('a.show-image-{{$item->id}}').html(data);
                    $(context).parent().find('.product-gif').hide();
                    $('a.show-image-{{$item->id}}').show();
                },
                error: function (request, status, error) {}
            });
        });
    });
</script>
