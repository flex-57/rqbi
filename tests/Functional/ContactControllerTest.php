<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ContactControllerTest extends WebTestCase
{
    private function validPayload(): array
    {
        return [
            'name'    => 'Jean Dupont',
            'email'   => 'jean@example.com',
            'subject' => 'Demande d\'information',
            'message' => 'Bonjour, je souhaite des informations.',
        ];
    }

    private function post(array $data): \Symfony\Bundle\FrameworkBundle\KernelBrowser
    {
        $client = static::createClient();
        $client->request('POST', '/api/contact', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));
        return $client;
    }

    public function testContact_WithValidData_Returns201(): void
    {
        $client = $this->post($this->validPayload());

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue($data['success']);
    }

    public function testContact_MissingName_Returns422(): void
    {
        $payload = $this->validPayload();
        unset($payload['name']);
        $client = $this->post($payload);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('name', $data['errors']);
    }

    public function testContact_InvalidEmail_Returns422(): void
    {
        $payload = $this->validPayload();
        $payload['email'] = 'pas-un-email';
        $client = $this->post($payload);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('email', $data['errors']);
    }

    public function testContact_MissingSubject_Returns422(): void
    {
        $payload = $this->validPayload();
        unset($payload['subject']);
        $client = $this->post($payload);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('subject', $data['errors']);
    }

    public function testContact_MissingMessage_Returns422(): void
    {
        $payload = $this->validPayload();
        unset($payload['message']);
        $client = $this->post($payload);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $data['errors']);
    }

    public function testContact_EmptyBody_Returns422WithAllErrors(): void
    {
        $client = $this->post([]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('name',    $data['errors']);
        $this->assertArrayHasKey('email',   $data['errors']);
        $this->assertArrayHasKey('subject', $data['errors']);
        $this->assertArrayHasKey('message', $data['errors']);
    }
}
