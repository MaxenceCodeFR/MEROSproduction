<?php

namespace App\Controller\Influencer;


use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/landing-influencer', name: 'influencer_')]
class InfluencerLandingController extends AbstractController
{
    #[Route('/all', name: 'all')]
    public function influencerLanding(UserRepository $userRepository): Response
    {
        $influencers = $userRepository->findRoleInfluencer();


        return $this->render('influencer/landing/user_landing.html.twig', compact('influencers'));
    }

    #[Route('/{id}', name: 'show')]
    public function show(User $user): Response
    {
        $promoted = $user->getPromotedLinks();//Récuperer les liens promus de l'influencer


        return $this->render('influencer/landing/show.html.twig', compact('promoted', 'user'));
    }
}