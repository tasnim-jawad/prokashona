<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionSupplierCategory extends Model
{
    use HasFactory;

    protected $appends = [
        'show_suppliers',
        'selected_id',
        'selected_name',
        'amount',
        'price',
        'order_number',
        'comment',
    ];

    public function suppliers()
    {
        return $this->hasMany(ProductionSupplier::class,'category_id','id');
    }

    public function getShowSuppliersAttribute()
    {
        return false;
    }
    public function getSelectedIdAttribute()
    {
        return null;
    }
    public function getSelectedNameAttribute()
    {
        return null;
    }
    public function getAmountAttribute()
    {
        return null;
    }
    public function getPriceAttribute()
    {
        return null;
    }
    public function getOrderNumberAttribute()
    {
        return null;
    }
    public function getCommentAttribute()
    {
        return null;
    }
}
