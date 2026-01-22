<?php

namespace App\Modules\Authentication\Infrastructure\Persistence\Eloquent\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\ModelHasRole;
use App\Models\Role;
use App\Models\Status;
use App\Models\Structure;
use App\Modules\Authentication\Domain\Enums\UserStatus;
use App\Modules\Authentication\Domain\ValueObjects\Id;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        // 'fullname',
        'firstnames',
        'lastname',
        'gender',
        'email',
        'phone',
        'status_id',
        'phone_verified_at',
        'email_verified_at',
        'is_send_otp',
        'citoyen_id',
        'password',
        'auth_provider',
        'provider_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'id' => 'string',
        'status_id' => 'string',
    ];


    public $incrementing = false;

    protected $keyType = 'string';
    protected $primaryKey = 'id';
    protected $table = 'users';

    protected $guard_name = 'api';

    public function findForPassport($username)
    {
        return $this->where('phone', $username)
            ->orWhere('email', $username)
            ->first();
    }

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

    public function structures()
    {
        return $this->belongsToMany(Structure::class, 'user_structures', 'user_id', 'structure_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }



    //refering to laravel spatie roles, it also heplps to add created_by and updated_by
    public function roles()
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles')
            ->using(ModelHasRole::class)
            ->withTimestamps();
    }
}
