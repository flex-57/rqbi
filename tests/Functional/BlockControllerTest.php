<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class BlockControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private string $token = '';
    private int $pageId;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->token = $this->fetchToken();
        $this->pageId = $this->fetchPageId();
    }

    private function fetchToken(): string
    {
        $this->client->request('POST', '/api/auth/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'admin@rqbi.fr',
            'password' => 'admin',
        ]));
        $data = json_decode($this->client->getResponse()->getContent(), true);
        return $data['token'] ?? '';
    }

    private function fetchPageId(): int
    {
        $this->client->request('GET', '/api/pages/slug/accueil');
        return json_decode($this->client->getResponse()->getContent(), true)['id'];
    }

    private function authHeader(): array
    {
        return ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token];
    }

    public function testTypes_ReturnsAllBlockTypes(): void
    {
        $this->client->request('GET', '/api/blocks/types', [], [], $this->authHeader());

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
        $this->assertCount(6, $data);
        $values = array_column($data, 'value');
        $this->assertContains('text', $values);
        $this->assertContains('image', $values);
    }

    public function testCreateBlock_WithValidData_CreatesBlock(): void
    {
        $this->client->request('POST', "/api/pages/{$this->pageId}/blocks", [], [], $this->authHeader(), json_encode([
            'type' => 'text',
            'content' => ['title' => 'Test', 'body' => 'Contenu test'],
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('text', $data['type']);
        $this->assertArrayHasKey('id', $data);
    }

    public function testCreateBlock_WithInvalidType_Returns422(): void
    {
        $this->client->request('POST', "/api/pages/{$this->pageId}/blocks", [], [], $this->authHeader(), json_encode([
            'type' => 'invalid',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateBlock_WithoutAuth_Returns401(): void
    {
        $this->client->request('POST', "/api/pages/{$this->pageId}/blocks", [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['type' => 'text']));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
