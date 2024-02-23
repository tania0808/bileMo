<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 15; ++$i) {
            $user = new User();
            $user->setFirstName('user_first_name'.$i);
            $user->setLastName('user_last_name'.$i);
            $user->setEmail('user'.$i.'@gmail.com');
            $user->setClient($this->getReference('client_'.rand(0, 14)));
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setUpdatedAt(new \DateTimeImmutable());

            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ClientFixtures::class,
        ];
    }
}
