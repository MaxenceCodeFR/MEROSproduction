<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class TeamController extends AbstractController
{

    #[Route('/team', name: 'team')]
    public function index(UserRepository $userRepository): Response
    {
        //Je récupère tous les utilisateurs ayant le rôle ROLE_CEO
        //grace a une query builder dans le UserRepository
        $teams = $userRepository->findUsersWithRoleCEO();
        dump($teams);

        return $this->render('team/index.html.twig', compact('teams'));
    }
}
