<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'total_price',
        'description',
        'status',
    ];

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function products() {
        return $this->belongsToMany('App\Models\Product', 'product_order')
            ->withPivot('quantity');
    }

    /**
     * Scope a query get new order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNewOrder($query)
    {
        return $query->where('status', 1);
    }
}
