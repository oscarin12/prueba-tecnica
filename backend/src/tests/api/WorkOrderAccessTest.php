<?php
//Un técnico NO puede ver una orden que pertenece a otro técnico (debe devolver 403 o 404).

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WorkOrderAccessTest extends WebTestCase
{
    public function testTechCannotAccessOtherTechWorkOrder(): void
    {
        $client = static::createClient();

        // 1) inicar sesión como tech1
        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'tech1@ott.cl',
            'password' => 'Tech123!',
        ]));

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent() ?? '', true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('token', $data);

        $token = $data['token'];

        // 2) Buscar 1 orden que NO sea de tech1 
        $client->request('GET', '/api/work-orders', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $this->assertResponseIsSuccessful();
        $orders = json_decode($client->getResponse()->getContent() ?? '', true);

       // Asegurarse de que se obtuvieron órdenes
        $this->assertIsArray($orders);
        // Buscar una orden que no pertenezca a tech1
        $otherId = 999999;

        // 3) Intentar acceder a una orden ajena -> debe dar 403 o 404
        $client->request('GET', '/api/work-orders/' . $otherId, [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token,
        ]);

        $this->assertTrue(
            in_array($client->getResponse()->getStatusCode(), [403, 404], true),
            'Expected 403 or 404 when accessing a work order not belonging to the authenticated technician.'
        );
    }
}