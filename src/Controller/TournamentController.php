<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use App\Entity\Tournament;
use App\Form\TournamentForm;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TournamentController extends AbstractController
{
    #[Route('/tournament/create', name : 'tournament.create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        // @TODO changer ce fonctionnement
        $user = $this->getUser();

        $tournament = new Tournament();
        $form = $this->createForm(TournamentForm::class, $tournament);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tournament = $form->getData();
            /** Passer par le formulaire */
            $tournament->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt(new DateTimeImmutable());
            $em->persist($tournament);
            $em->flush();
            return $this->redirectToRoute('tournament.dashboard');
        }

        return $this->render('tournament/create.html.twig', [
            'firstname' => $user->getfirstname(),
            'lastname' => $user->getlastname(),
            'tournamentForm' => $form,
        ]);
    }
}