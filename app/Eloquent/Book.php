<?php

namespace App\Eloquent;

class Book extends AbstractEloquent
{
    const STATUS = [
        'waiting' => 1,
        'reading' => 2,
        'done' => 3,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'author',
        'publish_date',
        'total_page',
        'avg_star',
        'code',
        'count_view',
        'status',
        'owner_id',
        'category_id',
        'office_id',
    ];

    protected $hidden = ['owner_id', 'category_id', 'office_id'];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('status', 'type');
    }

    public function userReadingBook()
    {
        return $this->belongsToMany(User::class)->withPivot('status', 'type')->wherePivot('status',2);
    }

    public function usersWaitingBook()
    {
        return $this->belongsToMany(User::class)->withPivot('status', 'type')->wherePivot('status',1);
    }

    public function reviews()
    {
        return $this->belongsToMany(User::class, 'reviews')->withPivot('content', 'star');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'target');
    }

    public function image()
    {
        return $this->morphOne(Media::class, 'target')->where('type', Media::TYPE_IMAGE_BOOK);
    }

    public function scopeGetData($query, $field, $orderBy = 'DESC')
    {
        return $query->where(function ($query) use ($field) {
            if ($field == 'count_view') {
                $query->where('count_view', '>', 0);
            }

            if ($field == 'avg_star') {
                $query->where('avg_star', '>', 0);
            }
        })->orderBy($field, $orderBy);
    }
}
