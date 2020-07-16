<?php

use Faker\Factory;
use Illuminate\Support\Facades\Log;


class InstagramControllerTest extends TestCase
{
    /**
     * @test
     */
    public function can_return_a_collection_of_paginated_20_posts(){
        $response = $this->json('GET', '/v1/posts/limit/20');
        Log::info(1, [$response->response->getContent()]);
        $response->assertResponseStatus(200);
    }
}
