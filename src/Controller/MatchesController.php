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
    public function index(Tournament $tournament, int $turn, TeamsRepository $teamrepository, MatchesRepository $matchesrepository, EntityManagerInterface $em): Response
    {
        // If teams are not already created
        if(!($teams = $teamrepository->findBy(['idtournament' => $tournament->getId()]))){
            $teams_created = $this->makeTeams($tournament->getNbjoueurs());
            // Create each Teams
            foreach($teams_created as $team_created){
                $team = new Teams();
                $team
                    ->setPlayer1($team_created['player1'])
                    ->setPlayer2($team_created['player2'])
                    ->setIdtournament($tournament->getId())
                    ->setPlayed(0)
                ;
                $em->persist($team);
                $em->flush();
                $teams[] = $team;
            }
        }

        // Must create match for better performance
        $exclude_players = '';
        if(empty($teamrepository->findBy(['idtournament' => $tournament->getId(), 'played' => 0]))){ // If there is no more teams to play
            $endoftournament = true;
        }else{
            if($played_matches = $matchesrepository->findBy(['idtournament' => $tournament->getId(), 'turn' => $turn])){ // Check if we had previous matches
                foreach($played_matches as $key => $played_match){
                    $matches_played[$key]['teams'][0] = $teamrepository->findOneBy(['id' => $played_match->getIdTeam1()]);
                    $matches_played[$key]['teams'][1] = $teamrepository->findOneBy(['id' => $played_match->getIdTeam2()]);
                    $matches_played[$key]['terrain'] = $key + 1;
                }
            }else{ // Create new matches
                for($i = 0; $i < $tournament->getNbterrains(); $i++){
                    if($i == 0){
                        $team1 = $teamrepository->findOneBy(['idtournament' => $tournament->getId(), 'played' => 0]);
                        $exclude_players .= $team1->getPlayer1() . ',' . $team1->getPlayer2();
                        $team2 = $teamrepository->getTeamByExcludedPlayers($exclude_players, $tournament->getId());
                        $exclude_players .= ',' . $team2->getPlayer1() . ',' . $team2->getPlayer2() . ',';
                    }else{
                        $team1 = $teamrepository->getTeamByExcludedPlayers(rtrim($exclude_players, ','), $tournament->getId());
                        $exclude_players .= $team1->getPlayer1() . ',' . $team1->getPlayer2() . ',';
                        $team2 = $teamrepository->getTeamByExcludedPlayers(rtrim($exclude_players, ','), $tournament->getId());
                        $exclude_players .= $team2->getPlayer1() . ',' . $team2->getPlayer2() . ',';
                    }
                    $match = new Matches();
                    $match                    
                        ->setIdteam1($team1->getId())
                        ->setIdteam2($team2->getId())
                        ->setIdtournament($tournament->getId())
                        ->setTurn($turn)
                    ;
                    $em->persist($match);
                    $em->flush();
                    $matches_played[$i]['teams'][0] = $teamrepository->findOneBy(['id' => $match->getIdTeam1()]);
                    $matches_played[$i]['teams'][0]
                        ->setPlayed(1);
                    $em->persist($matches_played[$i]['teams'][0]);
                    $em->flush();
                    $matches_played[$i]['teams'][1] = $teamrepository->findOneBy(['id' => $match->getIdTeam2()]);
                    $matches_played[$i]['teams'][1]
                        ->setPlayed(1);
                    $em->persist($matches_played[$i]['teams'][0]);
                    $em->flush();
                    $matches_played[$i]['terrain'] = $i + 1;
        
                }
            }
        }

        return $this->render('turn/show.html.twig', [
            'matches' => $matches_played ?? [],
            'turn' => $turn,
            'tournamentid' => $tournament->getId(),
            'endoftournament' => $endoftournament ?? false
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
        shuffle($teams);
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
                $team1Players = [$team1->getPlayer1(), $team1->getPlayer2()];
                sort($team1Players);

                $team2Players = [$team2->getPlayer1(), $team2->getPlayer2()];
                sort($team2Players);
                
                // Si les équipes n'ont pas de joueurs en commun, on crée un match
                if ($team1Players != $team2Players) {
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
