<?php

namespace App\DataFixtures;
use Faker\Factory;
use App\Entity\Event;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class EventFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker=Factory::create('fr_FR');
        for($i=1;$i<=50;$i++){
            $event = new Event();
            $event->setTitle($faker->sentence(3)); // Génère un titre aléatoire
            $event->setBeginAt($faker->dateTimeBetween('+1 week', '+1 month')); // Date de début aléatoire entre une semaine et un mois à partir de maintenant
            $event->setEndAt($faker->dateTimeBetween('+1 week', '+1 month')); // Date de fin aléatoire entre la date de début et un jour après
            $event->setLocation($faker->address); // Adresse aléatoire

            $manager->persist($event);}

        $manager->flush();
    }
}
