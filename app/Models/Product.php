<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['name', 'description', 'category_id', 'main_image_id', 'attributes', 'sort_order'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'category_id', 'main_image_id'];

    protected $casts = [
        'attributes' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
            $model->sort_order = self::where('category_id', $model->category_id)
                ->whereNull('deleted_at')
                ->max('sort_order') + 1;
        });

        static::deleting(function ($model) {
            self::where('category_id', $model->category_id)
                ->where('sort_order', '>', $model->sort_order)
                ->whereNull('deleted_at')
                ->decrement('sort_order');
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->belongsToMany(Image::class, 'image_product')
                    ->using(ProductImage::class)
                    ->withPivot('sort_order')
                    ->orderBy('pivot_sort_order');
    }

    public function mainImage()
    {
        return $this->belongsTo(Image::class, 'main_image_id');
    }

    public function getAttributesTable()
    {
        return collect($this->attributes)->map(function ($value, $key) {
            return ['key' => $key, 'value' => $value];
        });
    }

    public function updateSortOrder($newSortOrder)
    {
        if ($this->sort_order == $newSortOrder) {
            return;
        }

        if ($this->sort_order < $newSortOrder) {
            self::where('category_id', $this->category_id)
                ->whereBetween('sort_order', [$this->sort_order + 1, $newSortOrder])
                ->whereNull('deleted_at')
                ->decrement('sort_order');
        } else {
            self::where('category_id', $this->category_id)
                ->whereBetween('sort_order', [$newSortOrder, $this->sort_order - 1])
                ->whereNull('deleted_at')
                ->increment('sort_order');
        }

        $this->sort_order = $newSortOrder;
        $this->save();
    }
}