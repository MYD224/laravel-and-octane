<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ticket extends Model
{
    protected $table = 'tickets';

    // UUID primary key
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    // allow mass assignment (match migration columns)
    protected $fillable = [
        'id',
        'bus_id',
        'user_id',
        'seat_number',
        'passenger_name',
        'price',
    ];

    protected $casts = [
        'id' => 'string',
        'bus_id' => 'string',
        'user_id' => 'string',
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

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'ticket_id', 'id');
    }
}