<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'imageable_id',
        'imageable_type'
    ];


    protected $appends = [
        'full_path',
    ];
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }


    public function getFullPathAttribute()
    {
        return url('storage/' . $this->path);
    }
}
