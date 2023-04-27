<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Team;
use Faker\Factory;

class TeamFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $teamsNames = ['Real Madrid', 'Manchester United'];
        for ($i = 0; $i < 2; $i++) {
            $team = new Team();
            $team->setName($teamsNames[$i]);
            $team->setCountry($faker->country);
            $team->setBalance($faker->numberBetween(1000000, 200000000));
            $this->addReference('team' . $i + 1, $team);
            $manager->persist($team);
        }

        $manager->flush();
    }
}
