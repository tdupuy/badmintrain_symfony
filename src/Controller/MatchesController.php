<?php

namespace App\Controller;

use App\Entity\Tournament;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MatchesController extends AbstractController
{
    #[Route('/tournament/{id}/matches', name : 'matches.show')]
    public function index(Tournament $tournament): Response
    {
        $teams = $this->makeTeams($tournament->getNbjoueurs());
        return $this->render('matches/show.html.twig', [
        ]);
    }

    public function makeTeams(int $nbplayers): Response
    {
        $string = '';
        for($i = 1; $i <= $nbplayers; $i++){
            for($x = 1; $x <= $nbplayers; $x++){
                if(isset($array[$i][$x])){
                    $string .= $i . '-' . $x . '\n';
                }
            }
        }
        return new Response($string);
    }
}
