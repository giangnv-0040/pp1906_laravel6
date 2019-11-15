<?php

if (!function_exists('showCartQuantity')) {
    function showCartQuantity() {
        $quantity = 0;

        if (auth()->check()) {
            if (session()->has('order_data')) {
                return session('order_data.quantity');
            }

            $currentUser = auth()->user();
            $newOrder = $currentUser->orders()
                ->newOrder()
                ->first();

            $quantity = $newOrder ? $newOrder->products->sum('pivot.quantity') : 0;

            // session(['product_quantity' => $quantity]);
        }

        return $quantity;
    }
}
