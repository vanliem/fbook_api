<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Eloquent\Book;

class BookTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetBooksByRatingSuccess()
    {
        $response = $this->call('GET', route('api.v0.books.index', ['field' => 'rating']), [], [], [], $this->getHeaders());

        $response->assertJsonStructure([
            'item' => [
                'total', 'per_page', 'current_page', 'next_page', 'prev_page', 'data'
            ],
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testGetBooksByLatestSuccess()
    {
        $response = $this->call('GET', route('api.v0.books.index', ['field' => 'latest']), [], [], [], $this->getHeaders());

        $response->assertJsonStructure([
            'item' => [
                'total', 'per_page', 'current_page', 'next_page', 'prev_page', 'data'
            ],
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testGetBooksByViewSuccess()
    {
        $response = $this->call('GET', route('api.v0.books.index', ['field' => 'view']), [], [], [], $this->getHeaders());

        $response->assertJsonStructure([
            'item' => [
                'total', 'per_page', 'current_page', 'next_page', 'prev_page', 'data'
            ],
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testGetBooksInvalid()
    {
        $response = $this->call('GET', route('api.v0.books.index', ['field' => 'viewa']), [], [], [], $this->getHeaders());
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description',
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 422,
            ]
        ])->assertStatus(422);
    }

    public function testShowBookWithBookInvalid()
    {
        $headers = $this->getHeaders();
        $response = $this->call('GET', route('api.v0.books.show', 'xxx'), [], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 404,
            ]
        ])->assertStatus(404);
    }

    public function testShowBookWithBookNotFound()
    {
        $headers = $this->getHeaders();
        $response = $this->call('GET', route('api.v0.books.show', 0), [], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 404,
            ]
        ])->assertStatus(404);
    }

    public function testShowBooksSuccess()
    {
        $headers = $this->getHeaders();
        $book = Book::first();

        $response = $this->call('GET', route('api.v0.books.show', $book->id), [], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    // Search
    public function testListBookSearchSuccess()
    {
        $headers = $this->getHeaders();
        $data = [
            'search' => [
                'field' => 'title',
                'keyword' => 'a',
            ],
            'conditions' => [
                [
                    'category' => [
                        1, 2, 3
                    ]
                ],
                [
                    'office' => [
                        1, 2, 3
                    ]
                ],
            ],
            'sort' => [
                'field' => 'Latest',
                'order_by' => 'desc',
            ],
        ];

        $response = $this->call('POST', 'api/v0/search', $data, [], [], $headers);
        $response->assertJsonStructure([
            'items' => [
                'total', 'per_page', 'current_page', 'next_page', 'prev_page', 'data'
            ],
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testListBookSearchWithNotInput()
    {
        $headers = $this->getHeaders();

        $response = $this->call('POST', 'api/v0/search', [], [], [], $headers);
        $response->assertJsonStructure([
            'items' => [
                'total', 'per_page', 'current_page', 'next_page', 'prev_page', 'data'
            ],
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testListBookSearchWithFieldInValid()
    {
        $headers = $this->getHeaders();
        $data = [
            'search' => [
                'field' => 'a',
                'keyword' => 'a',
            ],
            'conditions' => [
                [
                    'category' => [
                        1, 2, 3
                    ]
                ],
                [
                    'office' => [
                        1, 2, 3
                    ]
                ],
            ],
            'sort' => [
                'field' => 'a',
                'order_by' => 'a',
            ],
        ];

        $response = $this->call('POST', 'api/v0/search', $data, [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 422,
            ]
        ])->assertStatus(422);
    }

    public function testBookingStatusDoneSuccess()
    {
        $headers = $this->getHeaders();
        $book = Book::first();
        $user = $book->userReadingBook()->first();

        $newUpdate['book_id'] = $book->id;
        $newUpdate['status'] = config('model.book_user.status.done');
        $newUpdate['user_id'] = $user->id;

        $response = $this->call('POST', route('api.v0.books.booking', $book->id), ['item' => $newUpdate], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testBookingStatusWaitingSuccess()
    {
        $headers = $this->getHeaders();
        $book = Book::first();
        $user = $book->usersWaitingBook()->first();

        $newUpdate['book_id'] = $book->id;
        $newUpdate['status'] = config('model.book_user.status.done');
        $newUpdate['user_id'] = $user->id;

        $response = $this->call('POST', route('api.v0.books.booking', $book->id), ['item' => $newUpdate], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testBookingWithNewUserSuccess()
    {
        $headers = $this->getHeaders();
        $book = Book::first();
        $user = $this->createUser();

        $newUpdate['book_id'] = $book->id;
        $newUpdate['status'] = config('model.book_user.status.done');
        $newUpdate['user_id'] = $user->id;

        $response = $this->call('POST', route('api.v0.books.booking', $book->id), ['item' => $newUpdate], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testBookingWithBookNotOwner()
    {
        $headers = $this->getHeaders();

        $user = $this->createUser();

        $newUpdate['book_id'] = 0;
        $newUpdate['status'] = config('model.book_user.status.done');
        $newUpdate['user_id'] = $user->id;

        $response = $this->call('POST', route('api.v0.books.booking', 0), ['item' => $newUpdate], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 404,
            ]
        ])->assertStatus(404);
    }

}
