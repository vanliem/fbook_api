<?php

namespace App\Eloquent;

class BookUser extends AbstractEloquent
{
    protected $table = 'book_user';

    protected $fillable = [
        'type',
        'status',
    ];
}
