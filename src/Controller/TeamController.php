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
        //*Je récupère tous les utilisateurs ayant le rôle ROLE_CEO
        //!grace a une query builder dans le UserRepository
        $teams = $userRepository->findUsersWithRoleCEO();
        

        //Pour eviter de faire une boucle dans le twig
        //je vais créer un tableau d'objet avec les réseaux sociaux
        // Je boucle sur les utilisateurs stocker dans la varibale $teams
        foreach ($teams as $team) {
            //Je récupère leurs réseaux sociaux sachant que c'est une Collection
            $socialsCollection = $team->getSocial();
            //Initialisation d'un tableau vide pour stocker les informations de réseaux sociaux pour chaque membre de l'équipe.
            $team->socialNetworks = [];

            //Comme c'est une collection d'une relation ManyToMany
            //Je boucle sur la collection pour récupérer les réseaux sociaux
            //A faire quand l'erreur est : "PersistentCollection" to string conversion
            //Attention c'est un tableau d'objet indexé
            foreach ($socialsCollection as $social) {
                $team->socialNetworks[] = [
                    'Instagram' => $social->getInstagram(),
                    'Snapchat' => $social->getSnapchat(),
                    'TikTok' => $social->getTiktok(),
                    'Twitter' => $social->getTwitter(),
                    'Facebook' => $social->getFacebook(),
                    'Pinterest' => $social->getPinterest(),
                    'Website' => $social->getWebsite(),
                    'Youtube' => $social->getYoutube(),

                ];
            }
        }

        return $this->render('team/index.html.twig', compact('teams'));
    }
}
