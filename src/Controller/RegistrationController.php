<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Service\BreadcrumbService;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        EmailService $emailService,
        BreadcrumbService $breadcrumbService

    ): Response {

        $breadcrumbService->add('Accueil', $this->generateUrl('landing'));
        $breadcrumbService->add('S\'inscrire', $this->generateUrl('app_register'));

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(["ROLE_USER"]);
            //Obliger de mettre un token vide car ne peut pas être 'null'
            $user->setResetToken('');

            $emailService->sendEmailFromNoReply(
                $user->getEmail(),
                'Bienvenue chez MEROS Productions',
                'emails/welcome.html.twig',
                ['user' => $user]
            );

            $this->addFlash('success', 'Votre compte a bien été créé un mail de confirmation vous a été envoyé. Merci de votre confiance');

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('login');
        }
        $parameters = [
            'registrationForm' => $form->createView(),
            'breadcrumbs' => $breadcrumbService->getBreadcrumbs()
        ];
        return $this->render('registration/register.html.twig', $parameters);
    }
}
