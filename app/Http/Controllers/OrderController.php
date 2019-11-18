<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\ProductOrder;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirm;

class OrderController extends Controller
{

    public function store(Request $request)
    {
        $productId = $request->product_id;
        $product = Product::findOrFail($productId);
        $currentUserId = auth()->id();

        if (session()->has('product_data')) {
            $productData = session('product_data');
        } else {
            $productData = [];
        }

        try {
            // If exist product in order
            if (array_key_exists($product->id, $productData)) {
                $productData[$product->id]['quantity'] += 1;
            } else {
                $productData[$product->id] = [
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => 1,
                ];
            }

            session(['product_data' => $productData]);
        } catch (\Exception $e) {
            \Log::error($e);

            $result = [
                'status' => false,
                'quantity' => 0,
            ];

            return response()->json($result);
        }

        $quantity = array_sum(array_column($productData,'quantity'));

        $result = [
            'status' => true,
            'quantity' => $quantity,
        ];

        $orderData = [
            'user_id' => $currentUserId,
            'total_price' => $product->price,
            'description' => '',
            'quantity' => $quantity,
        ];

        if (session()->has('order_data')) {
            $orderData['total_price'] = session('order_data.total_price')
                + $product->price;
        }

        session(['order_data' => $orderData]);

        return response()->json($result);
    }

    /**
     * Caculate total price for orders.
     *
     * @param array $products
     * @return int $totalPrice;
     */
    public function totalPrice($products)
    {
        $totalPrice = 0;

        foreach ($products as $product) {
            $totalPrice += $product['price'] * $product['quantity'];
        }

        return $totalPrice;
    }

    /**
     * Caculate total quantity for orders.
     *
     * @param array $products
     * @return int $totalQuantity;
     */
    public function totalQuantity($products)
    {
        $totalQuantity = 0;

        foreach ($products as $product) {
            $totalQuantity += $product['quantity'];
        }

        return $totalQuantity;
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCart()
    {
        $products = session('product_data');

        return view('orders.show', compact('products'));
    }

    /**
     * Destroy a product in order
     *
     * @param  \App\Http\Requests\Request $request
     * @return \Illuminate\Http\Response
    */
    public function destroyProduct(Request $request)
    {
        $productId = $request->product_id;
        $currentUser = auth()->user();

        $products = session('product_data');
        $order = session('order_data');

        if (in_array($productId, $products)) {
            unset($products[$productId]);
        }

        session(['product_data' => $products]);

        $order['total_price'] = $this->totalPrice($products);
        $order['quantity'] = array_sum(array_column($products, 'quantity'));
        session(['order_data' => $products]);

        return response()->json([
            'status' => true,
            'total_price' => $totalPrice,
        ]);
    }

    /**
     * Update product quantity of order.
     *
     * @param  \App\Http\Requests\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $updateFlag = true;

        $productId = $request->product_id;
        $quantity = $request->quantity;

        $currentUser = auth()->user();
        $order = $currentUser->orders()->newOrder()->first();

        try {
            $order->products()
                ->updateExistingPivot($productId, ['quantity' => $quantity]);

            $totalPrice = $this->totalPrice($order);
            $order->update(['total_price' => $totalPrice]);
        } catch (\Exception $e) {
            \Log::error($e);

            $updateFlag = false;
        }

        return response()->json([
            'status' => $updateFlag,
            'total_price' => $totalPrice,
        ]);
    }

    /**
     * Confirm order.
     *
     * @param  \App\Http\Requests\Request $request
     * @return \Illuminate\Http\Response
     */
    public function confirm(Request $request)
    {
        $updateFlag = true;

        $currentUser = auth()->user();
        $order = $currentUser->orders()->newOrder()->first();

        try {
            $order->update(['status' => 2]);
            // TO DO send mail to user
            Mail::to($request->user())->send(new OrderConfirm($order));
        } catch (\Exception $e) {
            \Log::error($e);

            $updateFlag = false;
        }

        session()->forget('product_quantity');

        return response()->json([
            'status' => $updateFlag,
        ]);
    }
}
