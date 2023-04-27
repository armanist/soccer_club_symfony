<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Player;
use App\Entity\Team;
use Faker\Factory;

class PlayerFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        $team = $manager->getRepository(Team::class);
        $teams = $team->findAll();

        $faker = Factory::create();

        foreach ($teams as $key => $team) {
            for ($j = 0; $j <= 15; $j++) {
                $player = new Player();
                $player->setName($faker->name);
                $player->setTeam($this->getReference('team' . $key + 1));
                $player->setPrice($faker->numberBetween(50000, 150000));
                $player->setAvailableForSale(false);
                $player->setSurname($faker->name);
                $manager->persist($player);
            }

            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            TeamFixtures::class,
        ];
    }

}
