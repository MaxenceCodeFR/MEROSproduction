<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();


        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/redirectAfterLogin', name: 'redirect_login', methods: ['GET'])]
    public function redirectAfterLogin(): Response
    {
        if ($this->isGranted('ROLE_CEO')) {
            return $this->redirectToRoute('ceo_index');
        } elseif ($this->isGranted('ROLE_EDITOR')) {
            return $this->redirectToRoute('editor_index');
        } else {
            return $this->redirectToRoute('landing');
        }
    }

    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout(): never
    {
    }
}
