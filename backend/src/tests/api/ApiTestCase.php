<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class ApiTestCase extends WebTestCase
{
    protected function jsonRequest(string $method, string $url, array $data = [], array $headers = []): \Symfony\Bundle\FrameworkBundle\KernelBrowser
    {
        $client = static::createClient();
        $server = array_merge([
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], $headers);  

        $client->request($method, $url, [], [], $server, json_encode($data));
        return $client;
    }

    protected function loginAndGetToken(string $email, string $password): string
    {
        $client = $this->jsonRequest('POST', '/api/login', [
            'email' => $email,
            'password' => $password,
        ]);

        $this->assertResponseIsSuccessful();
        $payload = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('token', $payload);
        $this->assertNotEmpty($payload['token']);

        return $payload['token'];
    }

    protected function authHeader(string $token): array
    {
        return ['HTTP_AUTHORIZATION' => 'Bearer '.$token];
    }
}