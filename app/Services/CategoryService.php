<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{

    /**
     * Get list category
     *
     * @param Array $option ['order_by' => value, 'key' => value]
     * @return Collection
     */
    public function getList($option = [])
    {
        $query = new Category;

        if (isset($option['key'])) {
            $query = $query->where('name', 'like', '%' . $key . '%');
        }

        if (isset($option['order_by'])) {
            $query = $query->orderBy($option['order_by'], 'DESC');
        }

        return $query->get();
    }

    /**
     * Create a category
     *
     * @param Array $data ['user_id' => value, 'parent_id' => value, 'name' => value]
     * @return Boolean
     */
    public function create($data)
    {
        try {
            Category::create($data);
        } catch (\Exception $e) {
            \Log::error($e);

            return false;
        }

        return true;
    }

    /**
     * Update a category
     *
     * @param Int $int
     * @param Array $data ['user_id' => value, 'parent_id' => value, 'name' =>value]
     * @return Boolean
     */
    public function update($id, $data)
    {
        $category = Category::findOrFail($id);

        try {
            $category->update($data);
        } catch (\Exception $e) {
            \Log::error($e);

            return false;
        }

        return true;
    }
}
