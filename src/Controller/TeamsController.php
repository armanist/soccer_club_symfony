<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TeamRepository;
use App\Entity\Player;
use App\Entity\Team;

class TeamsController extends AbstractController
{
    #[Route('/teams/{offset?}', name: 'teams', methods: ['GET'])]
    public function index(TeamRepository $teamRepository, $offset): Response
    {
        $offset = max(0, $offset);

        $paginator = $teamRepository->getTeamPaginator($offset);
        return $this->render('teams/index.html.twig', [
            'controller_name' => 'TeamsController',
            'teams' => $paginator,
            'page_name' => 'Teams',
            'previous' => $offset - TeamRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + TeamRepository::PAGINATOR_PER_PAGE)
        ]);
    }

    #[Route('/buy-player/{teamId}', name: 'app_players', methods: ['GET'])]
    public function buyPlayer(TeamRepository $teamRepository, string $teamId): Response
    {
        $team = $teamRepository->find($teamId);

        if(empty($team)){
            return $this->redirect('/teams');
        }

        $teams = $teamRepository->createQueryBuilder('t')
            ->where('t.id != :id')
            ->setParameter('id', $team->getId())
            ->getQuery()
            ->getResult();

        return $this->render('teams/buy-player.html.twig', [
            'team' => $team,
            'teams' => $teams,
            'page_name' => 'Buy player',
        ]);
    }

    #[Route('/get-team-players', methods: ['POST'])]
    public function getTeamPlayers(TeamRepository $teamRepository, Request $request): Response
    {
        $parameters = json_decode($request->getContent(), true);
        $teamId = $parameters['id'];

        $team = $teamRepository->find($teamId);
        $players = $team->getPlayers()->filter(function (Player $player) {
            if ($player->isAvailableForSale()) {
                return $player;
            }
        });

        $items = [];

        foreach ($players as $player) {
            $items[] = [
                'id' => $player->getId(),
                'name' => $player->getName(),
                'price' => $player->getPrice(),
                'surname' => $player->getSurname(),
                'team_id' => $player->getTeam()->getId(),
            ];
        }

        return $this->json(['players' => $items]);
    }

    #[Route('/create-team', name: 'app_team_create', methods: ['GET', 'POST'])]
    public function create(EntityManagerInterface $entityManager, Request $request, ValidatorInterface $validator): RedirectResponse|Response
    {
        $errors = [];
        if ($request->isMethod('POST')) {
            $team = new Team();
            $team->setName($request->request->get('name'));
            $team->setCountry($request->request->get('country'));
            $team->setBalance($request->request->get('balance'));

            $errors = $validator->validate($team);

            if (!count($errors) > 0) {
                $entityManager->persist($team);
                $entityManager->flush();

                foreach ($request->request->get('players') as $playerItem) {
                    $player = new Player();
                    $player->setName($playerItem['name']);
                    $player->setSurname($playerItem['surename']);
                    $player->setPrice($playerItem['price']);
                    $player->setAvailableForSale(false);
                    $player->setTeam($team);

                    $entityManager->persist($player);
                }

                $entityManager->flush();

                return $this->redirect('/teams');
            }
        }

        return $this->render('teams/create-team.html.twig', [
            'page_name' => 'New team',
            'errors' => count($errors) ? $errors : null
        ]);
    }

}
