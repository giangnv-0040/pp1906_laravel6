<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Http\Requests\ProductRequest;

class ProductService
{
    /**
     * Get list products.
     *
     * @return collection
     */
    public function getListProducts()
    {
        return Product::all();
    }

    /**
     * Store product.
     *
     * @param array $data [category_id', 'name', 'content', 'quantity', 'price']
     * @return boolean | App\Models\Product
     */
    public function createProduct($data)
    {
        try {
            $product = Product::create($data);
        } catch (\Exception $e) {
            \Log::error($e);

            return false;
        }

        return $product;
    }
}
