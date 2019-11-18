<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Http\Requests\ProductRequest;
use App\Services\ProductService;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = $this->productService->getListProducts();

        return view('admin.products.index', ['products' => $products]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ProductRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $data = $request->only([
            'category_id',
            'name',
            'content',
            'quantity',
            'price',
        ]);

        $data['user_id'] = auth()->id();

        $product = $this->productService->createProduct($data);

        if (!$product) {
            return back()->withInput($data)->with('status', 'Create failed!');
        }

        return redirect('/admin/products/' . $product->id)
            ->with('status', 'Create success!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);

        $data = ['product' => $product];

        return view('admin.products.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        $data = [
            'product' => $product,
            'categories' => $categories,
        ];

        return view('admin.products.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
        $data = $request->only([
            'category_id',
            'name',
            'content',
            'quantity',
            'price',
        ]);

        $product = Product::findOrFail($id);

        try {
            $product->update($data);
        } catch (\Exception $e) {
            \Log::error($e);

            return back()->with('status', 'Update faild.');
        }

        return redirect('admin/products/' . $product->id)->with('status', 'Update success.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        try {
            $product->delete();
        } catch (\Exception $e) {
            \Log::error($e);

            return back()->with('status', 'Delete faild.');
        }

        return redirect('admin/products')->with('status', 'Delete success');
    }
}
