<?php
// src/Controller/Dashboard.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Tournament;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name : 'tournament.dashboard')]
    public function init(EntityManagerInterface $em): Response
    {
        $last_tournament = $em->getRepository(Tournament::class)->findOneBy([], ['createdAt' => 'DESC']);
        $user = $this->getUser();

        return $this->render('home/dashboard.html.twig', [
            'firstname' => $user->getfirstname(),
            'lastname' => $user->getlastname(),
            'lasttournament' => $last_tournament
        ]);
    }
}