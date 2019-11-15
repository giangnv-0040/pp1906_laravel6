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
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $productId = $request->product_id;
        $product = Product::findOrFail($productId);
        $currentUserId = auth()->id();

        $orderData = [
            'user_id' => $currentUserId,
            'total_price' => $product->price,
            'description' => '',
        ];

        $order = Order::where('user_id', $currentUserId)
            ->newOrder()
            ->first();

        try {
            if (is_null($order)) {
                $order = Order::create($orderData);
            }

            $productOrder = ProductOrder::where('order_id', $order->id)
                ->where('product_id', $product->id)
                ->first();

            if ($productOrder) { // If exist product in order
                $productOrder->increment('quantity', 1);
            } else {
                // Create product_order
                $product->orders()
                    ->attach($order->id, ['quantity' => 1, 'price' => $product->price]);
            }

            $totalPrice = $this->totalPrice($order);
            $order->update(['total_price' => $totalPrice]);
        } catch (\Exception $e) {
            \Log::error($e);

            $result = [
                'status' => false,
                'quantity' => 0,
            ];

            return response()->json($result);
        }

        $quantity = $order->products->sum('pivot.quantity');

        $result = [
            'status' => true,
            'quantity' => $quantity,
        ];

        session(['product_quantity' => $quantity]);

        return response()->json($result);
    }

    /**
     * Caculate total price for orders.
     *
     * @param App\Models\Order $order
     * @return int $totalPrice;
     */
    public function totalPrice($order)
    {
        $totalPrice = 0;

        foreach ($order->products as $product) {
            $totalPrice += $product->price * $product->pivot->quantity;
        }

        return $totalPrice;
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCart()
    {
        $currentUser = auth()->user();
        $order = $currentUser->orders()
            ->newOrder()
            ->first();

        return view('orders.show', compact('order'));
    }

    /**
     * Destroy a product in order
     *
     * @param  \App\Http\Requests\Request $request
     * @return \Illuminate\Http\Response
    */
    public function destroyProduct(Request $request)
    {
        $deleteFlag = true;

        $productId = $request->product_id;
        $currentUser = auth()->user();
        $order = $currentUser->orders()->newOrder()->first();

        try {
            $order->products()->detach($productId);

            $totalPrice = $this->totalPrice($order);
            $order->update(['total_price' => $totalPrice]);
        } catch (\Exception $e) {
            \Log::error($e);

            $deleteFlag = false;
        }

        return response()->json([
            'status' => $deleteFlag,
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
