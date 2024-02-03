<?php

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
class ClientFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 15; $i++) {
            $client = new Client();
            $client->setLibelle('client '.$i);
            $client->setEmail('client' . $i . '@gmail.com');
            $client->setPassword(
                $this->passwordHasher->hashPassword($client, 'admin123')
            );
            $client->setAddress('address '.$i);
            $client->setCreatedAt(new \DateTimeImmutable());
            $client->setUpdatedAt(new \DateTimeImmutable());

            $this->addReference('client_'.$i, $client);
            $manager->persist($client);
        }

        $manager->flush();
    }
}
