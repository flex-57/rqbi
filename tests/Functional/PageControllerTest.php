<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PageControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private string $token = '';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->token = $this->fetchToken();
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

    private function authHeader(): array
    {
        return ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token];
    }

    public function testList_ReturnsJsonWithPages(): void
    {
        $this->client->request('GET', '/api/pages');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testTree_ReturnsPageTree(): void
    {
        $this->client->request('GET', '/api/pages/tree');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
    }

    public function testShowBySlug_WithValidSlug_ReturnsPage(): void
    {
        $this->client->request('GET', '/api/pages/slug/accueil');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('blocks', $data);
    }

    public function testShowBySlug_WithInvalidSlug_Returns404(): void
    {
        $this->client->request('GET', '/api/pages/slug/page-inexistante');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testCreate_WithoutAuth_Returns401(): void
    {
        $this->client->request('POST', '/api/pages', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'title' => 'Test',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testCreate_WithAdminAuth_CreatesPage(): void
    {
        $this->client->request('POST', '/api/pages', [], [], $this->authHeader(), json_encode([
            'title' => 'Ma nouvelle page',
            'published' => true,
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('Ma nouvelle page', $data['title']);
        $this->assertSame('ma-nouvelle-page', $data['slug']);
    }

    public function testCreate_WithMissingTitle_Returns422(): void
    {
        $this->client->request('POST', '/api/pages', [], [], $this->authHeader(), json_encode([
            'published' => false,
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDelete_WithAdminAuth_DeletesPage(): void
    {
        $this->client->request('POST', '/api/pages', [], [], $this->authHeader(), json_encode([
            'title' => 'À supprimer',
        ]));
        $created = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->request('DELETE', '/api/pages/' . $created['id'], [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}
