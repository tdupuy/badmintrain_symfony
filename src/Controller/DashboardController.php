<?php
// src/Controller/Dashboard.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name : 'app_dashboard')]
    public function init(): Response
    {
        return $this->render('home/dashboard.html.twig', []);
    }
}