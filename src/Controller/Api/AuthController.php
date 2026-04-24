<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/auth', name: 'api_auth_')]
class AuthController extends AbstractController
{
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(#[CurrentUser] ?User $user): JsonResponse
    {
        // JWT is handled by lexik/jwt-authentication-bundle
        // This endpoint is just a placeholder — the firewall intercepts it
        if (!$user) {
            return $this->json(['error' => 'Identifiants invalides'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'user' => [
                'id'    => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ],
        ]);
    }

    #[Route('/me', name: 'me', methods: ['GET'])]
    public function me(#[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            return $this->json(null);
        }

        return $this->json([
            'id'    => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ]);
    }

    #[Route('/setup', name: 'setup', methods: ['POST'])]
    public function setup(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
    ): JsonResponse {
        // Only allow if no admin exists yet
        $existing = $userRepository->findOneBy([]);
        if ($existing !== null) {
            return $this->json(['error' => 'Configuration déjà effectuée'], Response::HTTP_FORBIDDEN);
        }

        $payload = json_decode($request->getContent(), true);
        if (empty($payload['email']) || empty($payload['password'])) {
            return $this->json(['error' => 'Email et mot de passe obligatoires'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = new User();
        $user->setEmail($payload['email']);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($hasher->hashPassword($user, $payload['password']));

        $em->persist($user);
        $em->flush();

        return $this->json(['success' => true, 'email' => $user->getEmail()], Response::HTTP_CREATED);
    }
}
