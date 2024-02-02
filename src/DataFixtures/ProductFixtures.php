<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 15; $i++) {
            $product = new Product();
            $product->setName('product '.$i);
            $product->setDescription('description '.$i);
            $product->setImageName('image_'.$i);
            $product->setPrice(mt_rand(10, 100));
            $product->setBrand('brand '.$i);
            $product->setSpecifications('specifications '.$i);
            $product->setStock(mt_rand(0, 100));
            $product->setCreatedAt(new \DateTimeImmutable());
            $product->setUpdatedAt(new \DateTimeImmutable());
            $manager->persist($product);
        }

        $manager->flush();
    }
}
