<?php

namespace App;

use App\Seller;
use App\Category;
use App\Transaction;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    const PRODUCT_AVAILABLE = 'available';
    const PRODUCT_NOT_AVAILABLE = 'not available';

    protected $fillable = [
        'name',
        'description',
        'quantity',
        'status',
        'image',
        'seller_id',
    ];

    public function checkStatus()
    {
        return $this->status == Product::PRODUCT_AVAILABLE;
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
