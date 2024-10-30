<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductImage extends Pivot
{
    protected $table = 'image_product';

    protected $fillable = ['image_id', 'product_id', 'sort_order'];
    
    public $incrementing = false;
    protected $keyType = 'string';
}