<?php

namespace App\Controller;

use App\Entity\Tournament;
use App\Form\TournamentFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $user = $this->getUser();

        $tournament = new Tournament();
        $form = $this->createForm(TournamentFormType::class, $tournament);

        return $this->render('home/home.html.twig', [
            'tournamentForm' => $form,
        ]);
    }
}
