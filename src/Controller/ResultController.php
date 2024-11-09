<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Tournament;
use App\Repository\MatchesRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ResultController extends AbstractController
{
    #[Route('/result/tournament/{id}', name: 'tournament.result')]
    public function index(
        Tournament $tournament, 
        MatchesRepository $matchesrepository
    ): Response
    {
        $results = [];
        // Get result for each player
        for($i = 1; $i <= $tournament->getNbjoueurs(); $i++){
            $nb_matches_played = $matchesrepository->getMatchesForPlayersByTournament($tournament->getId(), $i);
            $nb_wins = $matchesrepository->getWinsForPlayersByTournament($tournament->getId(), $i);
            $results[$i] = [
                'nbmatches' => $nb_matches_played,
                'nbwins'     => $nb_wins,
                'winrate'   => ($nb_matches_played > 0) ? round(($nb_wins / $nb_matches_played) * 100, 2) : 0
            ];
        }

        // Order by winrate
        uasort($results, function($a, $b) {
            return $b['winrate'] <=> $a['winrate'];
        });
  
        return $this->render('result/index.html.twig', [
            'results' => $results
        ]);
    }
}
