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

    protected $fillable = ['name', 'image_path', 'sortorder'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
            $model->sortorder = self::whereNull('deleted_at')->max('sortorder') + 1;
        });

        static::deleting(function ($model) {
            self::where('sortorder', '>', $model->sortorder)
                ->whereNull('deleted_at')
                ->decrement('sortorder');
        });
    }

    public function updateSortOrder($newSortOrder)
    {
        if ($this->sortorder == $newSortOrder) {
            return;
        }

        if ($this->sortorder < $newSortOrder) {
            self::whereBetween('sortorder', [$this->sortorder + 1, $newSortOrder])
                ->whereNull('deleted_at')
                ->decrement('sortorder');
        } else {
            self::whereBetween('sortorder', [$newSortOrder, $this->sortorder - 1])
                ->whereNull('deleted_at')
                ->increment('sortorder');
        }

        $this->sortorder = $newSortOrder;
        $this->save();
    }
}
