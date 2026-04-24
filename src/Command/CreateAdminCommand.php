<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: "Création d'un administrateur.",
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        $output->writeln("Bienvenue dans l'interface de création d'un admin !");

        // --- email ---
        do {
            $emailQuestion = new Question(
                'Entrez l\'adresse email : '
            );
            $email = $helper->ask($input, $output, $emailQuestion);

            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $output->writeln('<error>Adresse email invalide, veuillez réessayer !</error>');
                $email = null;
            }
        } while (!$email);

        // --- password ---
        do {
            $passwordQuestion = new Question(
                'Entrez le mot de passe <comment>- Doit contenir au moins 8 caractères, une majuscule, un chiffre et un caractère spécial.</comment> : '
            );
            $passwordQuestion->setHidden(true);
            $passwordQuestion->setHiddenFallback(false);
            $password = $helper->ask($input, $output, $passwordQuestion);

            $pattern = '/^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W_]).{8,}$/';
            if (!$password || !preg_match($pattern, $password)) {
                $output->writeln('<error>Mot de passe invalide, veuillez réessayer.</error>');
                $password = null;
            }
        } while (!$password);

        // --- confirm password ---
        do {
            $confirmPasswordQuestion = new Question('Répétez le mot de passe : ');
            $confirmPasswordQuestion->setHidden(true);
            $confirmPasswordQuestion->setHiddenFallback(false);
            $confirmPassword = $helper->ask($input, $output, $confirmPasswordQuestion);

            if ($password !== $confirmPassword) {
                $output->writeln('<error>Les mots de passe ne correspondent pas, veuillez réessayer.</error>');
                $confirmPassword = null;
            }
        } while (!$confirmPassword);

        // Remplace l'admin existant avec le même email
        $existing = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existing) {
            $this->entityManager->remove($existing);
            $this->entityManager->flush();
        }

        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('<info>Le nouvel administrateur a été créé avec succès !</info>');
        $output->writeln('Email : <comment>' . $user->getEmail() . '</comment>');
        $output->writeln('Connectez-vous sur <comment>http://127.0.0.1:8000/login</comment>');

        return Command::SUCCESS;
    }
}
