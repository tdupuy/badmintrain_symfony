<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use App\Entity\Tournament;
use App\Form\TournamentForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TournamentController extends AbstractController
{
    #[Route('/tournament/create', name : 'app_lucky')]
    public function create(Request $request): Response
    {
        $tournament = new Tournament();
        $form = $this->createForm(TournamentForm::class, $tournament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // @ TODO

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('tournament/create.html.twig', [
            'tournamentForm' => $form,
        ]);
    }
}