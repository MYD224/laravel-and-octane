<?php

namespace App\Modules\Authentication\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserConnexion extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'user_id',
        'token_id',
        'created_by_id',
        'updated_by_id'
    ];

    public $incrementing = false;

    protected $keyType = 'string';
    protected $primaryKey = 'id';
    protected $table = 'user_connexions';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generate UUID if not already set
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
}
