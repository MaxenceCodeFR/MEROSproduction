<?php

namespace App\Controller;

use App\Repository\PromotedLinkRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Landing extends AbstractController
{
    #[Route('/', name: 'landing')]
    public function index(UserRepository $userRepository): Response
    {
        $influencers = $userRepository->findAboutInfluencer();

        return $this->render('landing.html.twig', [
            'influencers' => $influencers,
        ]);
    }

    #[Route('/rgpd', name: 'rgpd')]
    public function rgpd(): Response
    {
        return $this->render('rgpd.html.twig');
    }
}
