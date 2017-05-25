<?php

namespace App\Eloquent;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'code',
        'position',
        'role',
        'office_id',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'created_at', 'updated_at', 'deleted_at'
    ];

    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function avatar()
    {
        return $this->morphOne(Media::class, 'target');
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function books()
    {
        return $this->belongsToMany(Book::class)->withPivot('status', 'type');
    }

    public function reviews()
    {
        return $this->belongsToMany(Book::class)->withPivot('content', 'star');
    }

    public function suggestions()
    {
        return $this->hasMany(Suggestion::class);
    }

    public function owners()
    {
        return $this->hasMany(Book::class, 'owner_id');
    }
}
