<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Image extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['alt', 'url'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'pivot'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'image_product')
                    ->using(ProductImage::class)
                    ->withPivot('sort_order')
                    ->orderBy('pivot_sort_order');
    }
}
