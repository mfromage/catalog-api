<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['name', 'image_path', 'sort_order'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
            $model->sort_order = self::whereNull('deleted_at')->max('sort_order') + 1;
        });

        static::deleting(function ($model) {
            self::where('sort_order', '>', $model->sort_order)
                ->whereNull('deleted_at')
                ->decrement('sort_order');
        });
    }

    public function updateSortOrder($newSortOrder)
    {
        if ($this->sort_order == $newSortOrder) {
            return;
        }

        if ($this->sort_order < $newSortOrder) {
            self::whereBetween('sort_order', [$this->sort_order + 1, $newSortOrder])
                ->whereNull('deleted_at')
                ->decrement('sort_order');
        } else {
            self::whereBetween('sort_order', [$newSortOrder, $this->sort_order - 1])
                ->whereNull('deleted_at')
                ->increment('sort_order');
        }

        $this->sort_order = $newSortOrder;
        $this->save();
    }
}
