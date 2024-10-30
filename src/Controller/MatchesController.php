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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MatchesController extends AbstractController
{

    private ?EntityManagerInterface $entityManager = null;

    #[Route('tournament/{id}/matches/{turn}', name : 'matches.show')]
    public function index(Tournament $tournament, int $turn, TeamsRepository $teamrepository, MatchesRepository $matchesrepository, EntityManagerInterface $em): Response
    {
        // Init entitymanager
        $this->entityManager = $em;
        
        // If teams are not already created
        if(!($teams = $teamrepository->findBy(['idtournament' => $tournament->getId()]))){
            $teams_created = $this->makeTeams($tournament->getNbjoueurs());
            // Save each Team
            foreach($teams_created as $team_created){
                $team = new Teams();
                $team
                    ->setPlayer1($team_created['player1'])
                    ->setPlayer2($team_created['player2'])
                    ->setIdtournament($tournament->getId())
                    ->setPlayed(0)
                    ->setWeight(0)
                ;
                $em->persist($team);
                $em->flush();
                $teams[] = $team;
            }
        }
        
        $matches_played = $this->makeMatches($tournament,$turn);

        $subs = [];
        // If there is no more match
        if($matches_played == 'end_of_tournament'){
            $endoftournament = true;
        }else{
            $teams_playing = $matchesrepository->getPlayersForTurn($turn, $tournament->getId());
            if((count($teams_playing) * 2) <= $tournament->getNbjoueurs()){ // If submanagement is needed
                $subs = $this->manageSubs($tournament->getNbjoueurs(), $teams_playing);
            }
        }
        return $this->render('turn/show.html.twig', [
            'matches' => $matches_played ?? [],
            'subs' => $subs,
            'turn' => $turn,
            'tournamentid' => $tournament->getId(),
            'endoftournament' => $endoftournament ?? false
        ]);
    }

    public function manageSubs(int $nbplayers, array $teams) : Array
    {
        if ($this->entityManager === null) {
            throw new \RuntimeException("EntityManager n'est pas initialisé");
        }else{
            $em = $this->entityManager;
            $matchesrepository = $this->entityManager->getRepository(Matches::class);
            $teamsrepository = $this->entityManager->getRepository(Teams::class);
        }
        $players = [];
        // Merge all the array returns to easier the management
        $players = array_merge(array_values($teams[0]) ,array_values($teams[1]));
        $i = 1;
        while(isset($teams[$i + 1])){
            $players = array_merge($players,array_values($teams[$i + 1]));
            $i++;
        }
        // Get the players who hasn't play this turn
        $notplaying_players = [];
        for($i = 1; $i <= $nbplayers; $i++){
            // Get All teams for player
            $teams_for_player = $teamsrepository->getTeamsByPlayer($i);
            // Formating Array
            $teamsid = array_map(fn($team) => $team->getId(), $teams_for_player);
            if(in_array($i, $players) === false){ // If player teams_for_player playing he's in subs. Add weight + 1 to maximise his chance to play
                $notplaying_players[] = $i;
                $query = $em->createQuery('
                    UPDATE App\Entity\Teams t
                    SET t.weight = t.weight + 1
                    WHERE t.id IN (:ids)
                ');
            }else{
                $query = $em->createQuery('
                    UPDATE App\Entity\Teams t
                    SET t.weight = 0
                    WHERE t.id IN (:ids)
                ');
            }
            $query->setParameter('ids', $teamsid);
            $query->execute();
        }
        return $notplaying_players;
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

    public function makeMatches(Tournament $tournament, int $turn) : Array|String
    {
        if ($this->entityManager === null) {
            throw new \RuntimeException("EntityManager n'est pas initialisé");
        }else{
            $em = $this->entityManager;
            $teamrepository = $this->entityManager->getRepository(Teams::class);
            $matchesrepository = $this->entityManager->getRepository(Matches::class);
        }

        $exclude_players = '';
        if($played_matches = $matchesrepository->findBy(['idtournament' => $tournament->getId(), 'turn' => $turn])){ // Check if we had previous matches
            foreach($played_matches as $key => $played_match){
                $matches_played[$key]['id'] = $played_match->getId();
                $matches_played[$key]['teams'][0] = $teamrepository->findOneBy(['id' => $played_match->getIdTeam1()]);
                $matches_played[$key]['teams'][1] = $teamrepository->findOneBy(['id' => $played_match->getIdTeam2()]);
                $matches_played[$key]['terrain'] = $key + 1;
            }
            // If we hade more courts thant matches played create empty match
            for($i = count($matches_played); $i < $tournament->getNbterrains(); $i++){
                $matches_played[$i]['teams'][0] = [];
                $matches_played[$i]['teams'][1] = [];
                $matches_played[$i]['terrain'] = $i + 1;
            }
            return $matches_played;
        }else{ // Create new matches
            if(empty($teamrepository->findBy(['idtournament' => $tournament->getId(), 'played' => 0]))){
                $tournament->setEnded(1);
                $em->flush();
                return 'end_of_tournament';
            }else{
                for($i = 0; $i < $tournament->getNbterrains(); $i++){
                    if($i >= ($tournament->getNbjoueurs() / 4)){ // If we have more courts than players available, courts are empty;
                        $matches_played[$i]['teams'][0] = [];
                        $matches_played[$i]['teams'][1] = [];
                        $matches_played[$i]['terrain'] = $i + 1;
                        continue;
                    }

                    if($i == 0){
                        $team1 = $teamrepository->findOneBy(['idtournament' => $tournament->getId(), 'played' => 0]);
                        $exclude_players .= $team1->getPlayer1() . ',' . $team1->getPlayer2();
                        if($finded_team = $teamrepository->getTeamByExcludedPlayers(rtrim($exclude_players, ','), $tournament->getId())){
                            $team2 = $finded_team;
                        }else{
                            $team2 = $teamrepository->getTeamByExcludedPlayers(rtrim($exclude_players, ','), $tournament->getId(), 1);
                            $team2->setReplayed(1);
                        }
                        $exclude_players .= ',' . $team2->getPlayer1() . ',' . $team2->getPlayer2() . ',';
                    }else{
                        if($finded_team = $teamrepository->getTeamByExcludedPlayers(rtrim($exclude_players, ','), $tournament->getId())){
                            $team1 = $finded_team;
                        }else{
                            $team1 = $teamrepository->getTeamByExcludedPlayers(rtrim($exclude_players, ','), $tournament->getId(), 1);
                            $team1->setReplayed(1);
                        }
                        $exclude_players .= $team1->getPlayer1() . ',' . $team1->getPlayer2() . ',';
                        if($finded_team = $teamrepository->getTeamByExcludedPlayers(rtrim($exclude_players, ','), $tournament->getId())){
                            $team2 = $finded_team;
                        }else{
                            $team2 = $teamrepository->getTeamByExcludedPlayers(rtrim($exclude_players, ','), $tournament->getId(), 1);
                            // If there is no at least two teams which can play we set an empty court
                            if(!$team2){
                                $matches_played[$i]['teams'][0] = [];
                                $matches_played[$i]['teams'][1] = [];
                                $matches_played[$i]['terrain'] = $i + 1;
                                continue;
                            }
                            $team2->setReplayed(1);
                        }
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
                    $matches_played[$i]['id'] = $match->getId();
                    $matches_played[$i]['teams'][0] = $team1;
                    $matches_played[$i]['teams'][0]
                        ->setPlayed(1);
                    $em->flush();
                    $matches_played[$i]['teams'][1] = $teamrepository->findOneBy(['id' => $match->getIdTeam2()]);
                    $matches_played[$i]['teams'][1]
                        ->setPlayed(1);
                    $em->flush();
                    $matches_played[$i]['terrain'] = $i + 1;
                }
            }
        }
        return $matches_played;
    }

    #[Route('/set-winner', name: 'set-winner', methods: ['POST'])]
    public function setWinner(Request $request, MatchesRepository $matchesrepository, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $teamid = $data['teamid'] ?? null;
        $matchid = $data['matchid'] ?? null;

        $match = $matchesrepository->findOneBy(['id' => $matchid]);
        $match->setWinnerteamid($teamid);
        $em->flush();
        // Votre logique ici
        return $this->json([
            'message' => 'Success',
            'teamid' => $teamid
        ]);
    }
}
