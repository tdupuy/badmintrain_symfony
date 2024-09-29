<?php
// src/Controller/Dashboard.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name : 'app_dashboard')]
    public function init(): Response
    {
        $user = $this->getUser();

        return $this->render('home/dashboard.html.twig', [
            'firstname' => $user->getfirstname(),
            'lastname' => $user->getlastname()
        ]);
    }
}