$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.add-to-cart').click(function() {
        var url = '/orders';

        var data = {
            'product_id': $(this).data('product-id')
        };

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(result) {
                $('.cart-number').text('[' + result.quantity + ']');
                alert('Order success!');
            },
            error: function() {
                alert('Please login before order!');
                // window.location.href = '/login';
            }
        });
    });
});
