<?php

namespace App\Controller;

use App\Entity\Tournament;
use App\Form\TournamentFormType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $tournament = new Tournament();
        $form = $this->createForm(TournamentFormType::class, $tournament);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tournament = $form->getData();
            $tournament
                ->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt(new DateTimeImmutable())
                ->setEnded(0)
                ->setAdmin(1)
            ;
            $em->persist($tournament);
            $em->flush();
            
            #return $this->redirectToRoute('tournament.dashboard');
        }

        return $this->render('home/home.html.twig', [
            'tournamentForm' => $form,
        ]);
    }
}
