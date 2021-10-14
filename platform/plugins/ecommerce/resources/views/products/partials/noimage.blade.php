<a href="javascript:void(0)" class="noImg" id="prod-img-{{$item->id}}"><img src="https://revamp.landbw.co/storage/img.jpg"/></a>
<img class="product-gif " style="display: none;" src="https://revamp.landbw.co/storage/double-ring-1s-200px.gif"/>
<a class="show-image" style="display: none;" href="{{ route('products.edit', $item->id) }}" title="{{ $item->name }}"></a>

<script>
    $(document).ready(function () {
        $("#prod-img-{{$item->id}}").click(function () {
            $(this).parent().find('.product-gif').show();
            $(this).parent().find('.noImg').hide();
            $.ajax({
                url: '{{ route('products.loadProductImage', $item->id) }}',
                success: function (data) {
                    $('a.show-image').html(data);
                    $('img.product-gif').hide();
                    $('a.show-image').show();
                },
                error: function (request, status, error) {}
            });
        });
    });
</script>
