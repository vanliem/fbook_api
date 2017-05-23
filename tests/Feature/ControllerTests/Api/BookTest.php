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
}
