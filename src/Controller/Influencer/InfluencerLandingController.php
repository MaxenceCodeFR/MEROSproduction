<?php

namespace App\Controller\Influencer;


use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\BreadcrumbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/landing-influencer', name: 'influencer_')]
class InfluencerLandingController extends AbstractController
{
    #[Route('/all', name: 'all')]
    public function influencerLanding(UserRepository $userRepository, BreadcrumbService $breadcrumbService): ?Response
    {
        $breadcrumbService->add('Accueil', $this->generateUrl('landing'));
        $breadcrumbService->add('Nos Talents', $this->generateUrl('influencer_all'));

        $influencers = $userRepository->findRoleInfluencer();

        $parameters = [
            'influencers' => $influencers,
            'breadcrumbs' => $breadcrumbService->getBreadcrumbs()
        ];
        return $this->render('influencer/landing/user_landing.html.twig', $parameters);
    }

    #[Route('/{id}', name: 'show')]
    public function show(User $user, BreadcrumbService $breadcrumbService): Response
    {
        $breadcrumbService->add('Accueil', $this->generateUrl('landing'));
        $breadcrumbService->add('Nos Talents', $this->generateUrl('influencer_all'));
        $breadcrumbService->add($user->getFirstname(), $this->generateUrl('influencer_show', ['id' => $user->getId()]));

        $promoted = $user->getPromotedLinks();//Récuperer les liens promus de l'influencer

        $paramaters = [
            'user' => $user,
            'promoted' => $promoted,
            'breadcrumbs' => $breadcrumbService->getBreadcrumbs()
        ];


        return $this->render('influencer/landing/show.html.twig', $paramaters);
    }
}