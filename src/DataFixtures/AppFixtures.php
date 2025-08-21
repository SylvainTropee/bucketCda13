<?php

namespace App\DataFixtures;

use App\Entity\Wish;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->addWishes($manager);
    }

    private function addWishes(ObjectManager $manager)
    {

        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 50; $i++) {

            $wish = new Wish();
            $wish->setTitle($faker->realText(10))
                ->setDateCreated($faker->dateTimeBetween('-6 month'))
                ->setAuthor($faker->firstName)
                ->setIsPublished($faker->boolean(80))
                ->setDescription(join(" ", $faker->sentences()));

            $manager->persist($wish);
        }
        $manager->flush();
    }


}
