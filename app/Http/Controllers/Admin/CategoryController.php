<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = $this->categoryService
            ->getList(['order_by' => 'created_at']);

        return view('admin.categories.index', ['categories' => $categories]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = $this->categoryService->getList();

        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CategoryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        $data = $request->only([
            'name',
            'parent_id',
        ]);

        $data['user_id'] = auth()->id();

        if ($data['parent_id'] === "0") {
            $data['parent_id'] = null;
            // unset($data['parent_id']);
        }

        try {
            $category = Category::create($data);
        } catch (\Exception $e) {
            \Log::error($e);

            return back()->withInput($data)->with('status', __('app.msg.create_failed'));
        }

        return redirect('/admin/categories/' . $category->id)
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
        $category = Category::findOrFail($id);

        $data = ['category' => $category];

        return view('admin.categories.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $categories = Category::all();
        $data = [
            'categories' => $categories,
            'category' => $category,
        ];

        return view('admin.categories.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, $id)
    {
        $data = $request->only([
            'name',
            'content',
            'quantity',
            'price',
        ]);

        $updateFlag = $this->categoryService->update($id, $data);

        if ($updateFlag) {
            return redirect('admin/categories/' . $category->id)
                ->with('status', 'Update success.');
        }

        return back()->with('status', 'Update faild.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        try {
            $category->delete();
        } catch (\Exception $e) {
            \Log::error($e);

            return back()->with('status', 'Delete faild.');
        }

        return redirect('admin/categories')->with('status', 'Delete success');
    }
}
