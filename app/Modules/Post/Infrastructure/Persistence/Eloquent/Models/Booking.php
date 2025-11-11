<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Booking extends Model {

     protected $table = 'tickets';

    // UUID primary key
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'seat_number', 'passenger_name', 'price', 'ticket_id'];


    protected $casts = [
        'id' => 'string',
        'seat_number' => 'string',
        'ticket_id' => 'string',
        // keeps precision for decimal column
        'price' => 'decimal:2',
    ];


    // generate UUID when creating if none provided
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}