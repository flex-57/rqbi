<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/contact', name: 'api_contact', methods: ['POST'])]
class ContactController extends AbstractController
{
    public function __construct(
        private readonly MailerInterface $mailer,
        #[Autowire('%env(CONTACT_EMAIL)%')] private readonly string $contactEmail,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        $errors = $this->validate($data);
        if ($errors) {
            return $this->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $email = (new Email())
            ->from('noreply@rqbi.fr')
            ->to($this->contactEmail)
            ->replyTo($data['email'])
            ->subject('[RQBI Contact] ' . $data['subject'])
            ->text(
                "Nom : {$data['name']}\n" .
                "Email : {$data['email']}\n\n" .
                $data['message']
            );

        $this->mailer->send($email);

        return $this->json(['success' => true], Response::HTTP_CREATED);
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (empty(trim($data['name'] ?? '')))    $errors['name']    = 'Le nom est obligatoire.';
        if (empty(trim($data['email'] ?? '')))   $errors['email']   = "L'email est obligatoire.";
        elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email invalide.';
        if (empty(trim($data['subject'] ?? ''))) $errors['subject'] = 'Le sujet est obligatoire.';
        if (empty(trim($data['message'] ?? ''))) $errors['message'] = 'Le message est obligatoire.';

        return $errors;
    }
}
