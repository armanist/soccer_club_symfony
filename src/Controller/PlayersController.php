<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Player;
use App\Entity\Team;

class PlayersController extends AbstractController
{
    #[Route('/buy-player', name: 'app_buy_player', methods: ['POST'])]
    public function buyPlayer (EntityManagerInterface $entityManager, Request $request): Response
    {
        $parameters = json_decode($request->getContent(), true);

        $teamToId = $parameters['to'];
        $playerId = $parameters['id'];

        $player = $entityManager->getRepository(Player::class)->find($playerId);
        $price = $player->getPrice();

        $team = $entityManager->getRepository(Team::class);
        $teamTo = $team->find($teamToId);
        $teamFrom = $team->find($player->getTeam()->getId());

        $teamTo->setBalance($teamTo->getBalance() - $price);
        $player->setTeam($teamTo);
        $teamTo->addPlayer($player);
        $entityManager->persist($teamTo);
        $entityManager->flush();

        $teamFrom->setBalance($teamFrom->getBalance() + $price);
        $teamFrom->removePlayer($player);
        $entityManager->persist($teamFrom);
        $entityManager->flush();

        $response = [
            'success' => true,
            'fromText' => $teamFrom->getName() .' ($'. $teamFrom->getBalance() .')',
            'toText' => $teamTo->getName() .' ($'. $teamTo->getBalance() .')'
        ];

        return $this->json($response);
    }

    #[Route('/change-is-available-sale', name: 'app_change_is_available_sale', methods: ['POST'])]
    public function changeIsAvailableSale (EntityManagerInterface $entityManager, Request $request): Response
    {
        $parameters = json_decode($request->getContent(), true);
        $playerId = $parameters['id'];
        $checked = $parameters['checked'];

        $playerEntity = $entityManager->getRepository(Player::class);
        $player = $playerEntity->find($playerId);

        $player->setAvailableForSale($checked);
        $entityManager->persist($player);
        $entityManager->flush();

        return $this->json(['success' => true]);
    }
}
