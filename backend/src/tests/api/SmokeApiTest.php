<?php

namespace App\Tests\Api;

class SmokeApiTest extends ApiTestCase
{
    public function testLoginReturnsToken(): void
    {
        $token = $this->loginAndGetToken('tech1@ott.cl', 'Tech123!');
        $this->assertNotEmpty($token);
    }
}