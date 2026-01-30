<?php

namespace App\Tests\Factory;

use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return User::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'username' => self::faker()->userName(),
            'roles' => ['ROLE_USER'],
            'password' => 'test_password',
            'isActive' => true,
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
            ->afterPersist(function(User $user): void {
                if (!$this->isPasswordHashed($user->getPassword())) {
                    $container = self::getContainer();
                    $hasher = $container->get('security.user_password_hasher');
                    $hashed = $hasher->hashPassword($user, $user->getPassword());
                    $user->setPassword($hashed);
                }
            });
    }

    private function isPasswordHashed(string $password): bool
    {
        return str_starts_with($password, '$2y$') || str_starts_with($password, '$argon2');
    }

    public function asAdmin(): self
    {
        return $this->with(['roles' => ['ROLE_ADMIN']]);
    }

    public function inactive(): self
    {
        return $this->with(['isActive' => false]);
    }
}
