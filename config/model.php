<?php

return [
    'book' => [
        'status' => [
            'unavailable' => 0,
            'available' => 1,
        ],
        'fields' => [
            'title',
            'description',
            'author',
            'publish_date',
            'total_page',
            'avg_star',
            'code',
            'count_view',
            'status',
            'created_at'
        ],
    ],
    'book_user' => [
        'status' => [
            'waiting' => 1,
            'reading' => 2,
            'done'    => 3,
        ]
    ],
    'filter_books' => [
        'view' => [
            'key' => 'view',
            'field' => 'count_view',
        ],
        'waiting' => [
            'key' => 'waiting',
            'field' => '',
        ],
        'rating' => [
            'key' => 'rating',
            'field' => 'avg_star',
        ],
        'latest' => [
            'key' => 'latest',
            'field' => 'created_at',
        ]
    ],
    'filter_type' => [
        'category', 'office'
    ],
    'sort_field' => [
        'rating' => 'avg_star',
        'latest' => 'created_at',
        'view' => 'count_view',
        'title' => 'title',
    ],
    'sort_type' => [
        'desc', 'asc'
    ],
];
