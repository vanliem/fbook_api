<?php

namespace App\Eloquent;

use Illuminate\Database\Eloquent\Model;

class BookUser extends Model
{
    protected $table = 'book_user';

    protected $fillable = [
        'type',
        'status',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
