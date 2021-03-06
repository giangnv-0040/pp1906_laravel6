<?php

if (!function_exists('showCartQuantity')) {
    function showCartQuantity() {
        $quantity = 0;

        if (auth()->check()) {
            if (session()->has('product_quantity')) {
                return session('product_quantity');
            }

            $currentUser = auth()->user();
            $newOrder = $currentUser->orders()
                ->newOrder()
                ->first();

            $quantity = $newOrder ? $newOrder->products->sum('pivot.quantity') : 0;

            session(['product_quantity' => $quantity]);
        }

        return $quantity;
    }
}

if (!function_exists('showProductImage')) {
    function showProductImage($image) {
        $imagePath = '/theme/images/product-1.jpg';

        if ($image) {
            $imagePath = asset('storage/products/' . $image);
        }

        return $imagePath;
    }
}
