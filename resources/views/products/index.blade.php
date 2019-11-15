@extends('layouts.shop')

@section('content')

    @include('products.partials.header_slide')

    <section class="ftco-section bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-lg-10 order-md-last">
                    <div class="row">
                        @foreach ($products as $product)
                        <div class="col-sm-6 col-md-6 col-lg-4 ftco-animate">
                            <div class="product">
                                <a href="#" class="img-prod"><img class="img-fluid" src="/theme/images/product-1.jpg" alt="Colorlib Template">
                                    <span class="status">30%</span>
                                    <div class="overlay"></div>
                                </a>
                                <div class="text py-3 px-3">
                                    <h3><a href="#">{{ $product->name }}</a></h3>
                                    <div class="d-flex">
                                        <div class="pricing">
                                            <p class="price"><span class="mr-2 price-dc">$120.00</span><span class="price-sale">${{ $product->price }}</span></p>
                                        </div>
                                        <div class="rating">
                                            <p class="text-right">
                                                <a href="#"><span class="ion-ios-star-outline"></span></a>
                                                <a href="#"><span class="ion-ios-star-outline"></span></a>
                                                <a href="#"><span class="ion-ios-star-outline"></span></a>
                                                <a href="#"><span class="ion-ios-star-outline"></span></a>
                                                <a href="#"><span class="ion-ios-star-outline"></span></a>
                                            </p>
                                        </div>
                                    </div>
                                    <p class="bottom-area d-flex px-3">
                                        <a href="#" class="add-to-cart text-center py-2 mr-1" data-product-id="{{ $product->id }}"><span>Add to cart <i class="ion-ios-add ml-1"></i></span></a>
                                        <a href="#" class="buy-now text-center py-2">Buy now<span><i class="ion-ios-cart ml-1"></i></span></a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    {{ $products->links('products.partials.paginate') }}
                </div>

                @include('products.partials.sidebar')
            </div>
        </div>
    </section>

@endsection

@section('js')
    <script type="text/javascript">
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
                    cache: false,
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
    </script>
@endsection
