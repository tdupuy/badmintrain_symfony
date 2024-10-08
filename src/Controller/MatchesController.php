<?php

namespace App\Controller;

use App\Entity\Tournament;
use App\Entity\Teams;
use App\Entity\Matches;
use App\Repository\TeamsRepository;
use App\Repository\MatchesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MatchesController extends AbstractController
{
    #[Route('tournament/{id}/matches/{turn}', name : 'matches.show')]
    public function index(Tournament $tournament, int $turn = 1, TeamsRepository $teamrepository, MatchesRepository $matchesrepository, EntityManagerInterface $em): Response
    {
        // If teams are not already created
        if(!($teams = $teamrepository->findBy(['idtournament' => $tournament->getId()]))){
            $teams_created = $this->makeTeams($tournament->getNbjoueurs());
            // Save each Teams
            foreach($teams_created as $team_created){
                $team = new Teams();
                $team
                    ->setPlayer1($team_created['player1'])
                    ->setPlayer2($team_created['player2'])
                    ->setIdtournament($tournament->getId())
                ;
                $em->persist($team);
                $em->flush();
                $teams[] = $team;
            }
        }

        // Then we need to create the matches
        if(!($matches = $matchesrepository->findBy(['idtournament' => $tournament->getId()]))){
            $matches_created = $this->makeMatches($teams);
            // Shuffle before insert
            // Save each Matches
            foreach($matches_created as $match_created){
                $match = new Matches();
                $match
                    ->setIdteam1($match_created['team1'])
                    ->setIdteam2($match_created['team2'])
                    ->setIdtournament($tournament->getId())
                    ->setPlayed(0)
                ;
                $em->persist($match);
                $em->flush();
                $matches[] = $match;
            }
        }
        
        // Exécution de la requête*/
        $str_to_exclude = '';
        for($i = 0; $i < $tournament->getNbterrains(); $i++){
            // If it's the first time
            if($i == 0){
                $match_selected = $matchesrepository->findOneBy(['played' => 0, 'idtournament' => $tournament->getId()]);
                $match_selected->setPlayed(1);
                $em->persist($match_selected);
                $em->flush();
                $str_to_exclude .= $this->excludeTeamsForMatches($match_selected->getIdTeam1(), $tournament->getId(), $teamrepository);
                $str_to_exclude .= $this->excludeTeamsForMatches($match_selected->getIdTeam2(), $tournament->getId(), $teamrepository);
            }else{
                $str_to_exclude = rtrim($str_to_exclude, ',');
                $match_selected = $matchesrepository->findAvailableMatchForTurn($tournament->getId(), $str_to_exclude);
                $match_selected->setPlayed(1);
                $em->persist($match_selected);
                $em->flush();
                $str_to_exclude .= $this->excludeTeamsForMatches($match_selected->getIdTeam1(), $tournament->getId(), $teamrepository);
                $str_to_exclude .= $this->excludeTeamsForMatches($match_selected->getIdTeam2(), $tournament->getId(), $teamrepository);
            }
            $matches_turn[$i] = $match_selected;

            if($i == 1){
                /*dd($str_to_exclude);
                dd($matches_turn);*/
            }

        }
        // Format matches_played
        foreach($matches_turn as $key => $match_turn){
            $matches_played[$key]['teams'][0] = $teamrepository->findOneBy(['id' => $match_turn->getIdTeam1()]);
            $matches_played[$key]['teams'][1] = $teamrepository->findOneBy(['id' => $match_turn->getIdTeam2()]);
            $matches_played[$key]['terrain'] = $key + 1;
        }

        return $this->render('turn/show.html.twig', [
            'matches' => $matches_played ?? []
        ]);
    }

    public function excludeTeamsForMatches(int $id_team, int $idtournament, TeamsRepository $teamrepository) : string
    {
        // Get all teams from team1 where the player belong to exclude it from request
        $str_to_exclude = '';
        $players_team1 = $teamrepository->findOneBy(['id' => $id_team]);
        $teams_to_exclude = $teamrepository->findBy(['player1' => $players_team1->getPlayer1(), 'idtournament' => $idtournament]);
        foreach($teams_to_exclude as $team_to_exclude){
            $str_to_exclude .= $team_to_exclude->getId() . ',';
        }
        /// If there is a 2nd player
        if($players_team1->getPlayer2()){
            $teams_to_exclude = $teamrepository->findBy(['player2' => $players_team1->getPlayer2(), 'idtournament' => $idtournament]);
            foreach($teams_to_exclude as $team_to_exclude){
                $str_to_exclude .= $team_to_exclude->getId() . ',';
            }
        }
        return $str_to_exclude;
    }

    public function makeTeams(int $nbplayers) : Array
    {
        $teams = [];

        for($i = 1; $i <= $nbplayers; $i++){
            for($y = $i + 1; $y <= $nbplayers; $y++){
                $teams[] = [
                    'player1' => $i, 
                    'player2' => $y
                ];
            }
        }
        return $teams;
    }

    public function makeMatches(Array $teams) : Array
    {
        $matchups = [];
    
        // Boucle pour la première équipe
        for ($i = 0; $i < count($teams); $i++) {
            // Boucle pour la deuxième équipe
            for ($j = $i + 1; $j < count($teams); $j++) {
                // Vérifier qu'il n'y a pas de joueurs communs entre les deux équipes
                $team1 = $teams[$i];
                $team2 = $teams[$j];
                // Si les équipes n'ont pas de joueurs en commun, on crée un match
                if ($team1->getPlayer1() != $team2->getPlayer1() 
                    && $team1->getPlayer1() != $team2->getPlayer2()
                    && $team1->getPlayer2() != $team2->getPlayer1()
                    && $team1->getPlayer2() != $team2->getPlayer2()
                ) {
                    $matchups[] = [
                        'team1' => $team1->getId(), 
                        'team2' => $team2->getId()
                    ];
                }
            }
        }
        return $matchups;
    }
}
