<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            [
                'email' => 'ivan@gmail.com',
                'roles' => ['ROLE_USER'],
                'password' => 'password123',
            ],
            [
                'email' => 'maria@gmail.com',
                'roles' => ['ROLE_USER'],
                'password' => 'password123',
            ],
            [
                'email' => 'admin@gmail.com',
                'roles' => ['ROLE_ADMIN'],
                'password' => 'admin123',
            ],
        ];

        foreach ($users as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setRoles($userData['roles']);

            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
            $user->setPassword($hashedPassword);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
