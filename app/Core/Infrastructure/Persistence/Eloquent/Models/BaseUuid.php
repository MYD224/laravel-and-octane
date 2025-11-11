<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UuidModel extends Model
{
    use HasFactory;
    use HasUuids;
    

    public $incrementing = false;

    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'string'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) \Illuminate\Support\Str::uuid();
        });
    }
}