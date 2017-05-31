<?php

namespace Tests\Feature\ControllerTests\Api;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseTransactions;

    public function testListCategoriesSuccess()
    {
        $headers = $this->getHeaders();
        $response = $this->call('GET', 'api/v0/categories', [], [], [], $headers);

        $response->assertJsonStructure([
            'message' => [
                'status', 'code'
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }
}
