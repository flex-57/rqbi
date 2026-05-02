<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private bool $needsRestore = false;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->needsRestore = false;
    }

    protected function tearDown(): void
    {
        if ($this->needsRestore) {
            $this->restoreAdmin();
        }
        parent::tearDown();
    }

    private function deleteAllUsers(): void
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $repo = static::getContainer()->get(UserRepository::class);

        foreach ($repo->findAll() as $user) {
            $em->remove($user);
        }
        $em->flush();
        $em->clear();
    }

    private function restoreAdmin(): void
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        $repo = static::getContainer()->get(UserRepository::class);

        foreach ($repo->findAll() as $user) {
            $em->remove($user);
        }
        $em->flush();

        $user = new User();
        $user->setEmail('admin@rqbi.fr');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($hasher->hashPassword($user, 'admin'));
        $em->persist($user);
        $em->flush();
        $em->clear();
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

    public function testLogin_WithValidCredentials_ReturnsToken(): void
    {
        $this->client->request('POST', '/api/auth/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'admin@rqbi.fr',
            'password' => 'admin',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);
        $this->assertNotEmpty($data['token']);
    }

    public function testLogin_WithInvalidCredentials_Returns401(): void
    {
        $this->client->request('POST', '/api/auth/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'admin@rqbi.fr',
            'password' => 'wrongpassword',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testMe_WithValidToken_ReturnsUser(): void
    {
        $token = $this->fetchToken();

        $this->client->request('GET', '/api/auth/me', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertSame('admin@rqbi.fr', $data['email']);
        $this->assertContains('ROLE_ADMIN', $data['roles']);
    }

    public function testMe_WithoutToken_ReturnsNull(): void
    {
        $this->client->request('GET', '/api/auth/me');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertNull(json_decode($this->client->getResponse()->getContent(), true));
    }

    public function testSetup_WhenAdminAlreadyExists_ReturnsForbidden(): void
    {
        $this->client->request('POST', '/api/auth/setup', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'new@rqbi.fr',
            'password' => 'newpassword',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function testSetup_WhenNoAdminExists_CreatesAdmin(): void
    {
        $this->needsRestore = true;
        $this->deleteAllUsers();

        $this->client->request('POST', '/api/auth/setup', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'new@rqbi.fr',
            'password' => 'securepassword',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($data['success']);
        $this->assertSame('new@rqbi.fr', $data['email']);
    }

    public function testSetup_WithMissingCredentials_Returns422(): void
    {
        $this->needsRestore = true;
        $this->deleteAllUsers();

        $this->client->request('POST', '/api/auth/setup', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'new@rqbi.fr',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }
}
