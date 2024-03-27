<?php

namespace App\Controller;

use App\Repository\PromotedLinkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Landing extends AbstractController
{
    #[Route('/', name: 'landing')]
    public function index(): Response
    {
        return $this->render('landing.html.twig');
    }

    #[Route('/rgpd', name: 'rgpd')]
    public function rgpd(): Response
    {
        return $this->render('rgpd.html.twig');
    }
}
