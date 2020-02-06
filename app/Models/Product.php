<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'user_id',
        'name',
        'content',
        'quantity',
        'price',
        'image',
    ];

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function orders() {
        return $this->belongsToMany('App\Models\Order', 'product_order');
    }

    public function category() {
        return $this->belongsTo('App\Models\Category');
    }
}
