<?php

// src/Controller/Dashboard.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Tournament;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name : 'tournament.dashboard')]
    public function init(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $last_tournament = $em->getRepository(Tournament::class)->findOneBy(['ended' => 1, 'admin' => $user->getId()], ['createdAt' => 'DESC']);

        return $this->render('home/dashboard/content.html.twig', [
            'firstname' => $user->getfirstname(),
            'lastname' => $user->getlastname(),
            'lasttournament' => $last_tournament ? $last_tournament : '',
        ]);
    }
}
