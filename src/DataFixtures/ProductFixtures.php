<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $brands = ['Samsung', 'Apple', 'Google', 'OnePlus', 'Sony', 'Huawei', 'Xiaomi', 'LG', 'Motorola', 'Nokia'];
        $descriptions = [
            'Exceptional performance and cutting-edge technology.',
            'Elegantly designed with the latest features for a seamless experience.',
            'Capture every moment with the advanced camera system.',
            'Innovative design and top-notch build quality.',
            'Revolutionary features for the tech-savvy user.',
            'Unmatched performance and stunning display.',
            'Powerful and stylish, a perfect blend of form and function.',
            'Next-level connectivity and intelligent features.',
            'Premium materials and craftsmanship for a luxurious feel.',
            'Experience the future with groundbreaking technology.'
        ];
        $specifications = [
            'Processor: Octa-core, RAM: 8GB, Storage: 256GB, Camera: Triple-lens 48MP+12MP+8MP',
            'Processor: Hexa-core, RAM: 6GB, Storage: 128GB, Camera: Dual-lens 64MP+12MP',
            'Processor: Octa-core, RAM: 12GB, Storage: 512GB, Camera: Quad-lens 108MP+20MP+12MP+5MP',
            'Processor: Octa-core, RAM: 8GB, Storage: 128GB, Camera: Triple-lens 50MP+16MP+8MP',
            'Processor: Octa-core, RAM: 6GB, Storage: 256GB, Camera: Dual-lens 48MP+16MP',
            'Processor: Octa-core, RAM: 10GB, Storage: 256GB, Camera: Penta-lens 64MP+48MP+12MP+5MP+2MP',
            'Processor: Octa-core, RAM: 8GB, Storage: 256GB, Camera: Triple-lens 64MP+12MP+8MP',
            'Processor: Octa-core, RAM: 6GB, Storage: 128GB, Camera: Dual-lens 48MP+8MP',
            'Processor: Octa-core, RAM: 12GB, Storage: 512GB, Camera: Quad-lens 108MP+20MP+8MP+2MP',
            'Processor: Octa-core, RAM: 8GB, Storage: 256GB, Camera: Triple-lens 48MP+16MP+5MP'
        ];

        for ($i = 0; $i < 20; $i++) {
            $product = new Product();
            $brandIndex = array_rand($brands);
            $descriptionIndex = array_rand($descriptions);
            $specificationsIndex = array_rand($specifications);

            $product->setName($brands[$brandIndex] . ' Phone ' . $i);
            $product->setDescription($descriptions[$descriptionIndex]);
            $product->setImageName('phone_image_'.$i);
            $product->setPrice(mt_rand(500, 1500));
            $product->setBrand($brands[$brandIndex]);
            $product->setSpecifications($specifications[$specificationsIndex]);
            $product->setStock(mt_rand(10, 50));
            $product->setCreatedAt(new \DateTimeImmutable());
            $product->setUpdatedAt(new \DateTimeImmutable());
            $manager->persist($product);
        }

        $manager->flush();
    }
}
