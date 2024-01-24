<?php

namespace App\Controller;

use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use App\Form\ResetPasswordRequestType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

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
        //redirection en fonction du role
        //Si le rôle est CEO on redirige vers la page d'accueil du CEO
        if ($this->isGranted('ROLE_CEO')) {
            return $this->redirectToRoute('ceo_index');
            //Si le role est editeur alors il ira vers le blog
        } elseif ($this->isGranted('ROLE_EDITOR')) {
            return $this->redirectToRoute('editor_index');
        } else {
            //Si le role est user ou en dessous de editeur alors il ira vers la page d'accueil
            return $this->redirectToRoute('landing');
        }
    }

    //Déconnexion
    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout(): never
    {
    }

    //Mot de passe oublié
    #[Route('/reset-password', name: 'reset_password')]
    public function resetPassword(
        Request $request,
        UserRepository $userRepository,
        TokenGeneratorInterface $token,
        EntityManagerInterface $em,
        MailerInterface $mail
    ): Response {
        //Je crée le formulaire de demande de réinitialisation de mot de passe
        //En demandant le mail de l'utilisateur
        //cf. src/Form/ResetPasswordRequestType.php
        $form = $this->createForm(ResetPasswordRequestType::class);

        $form->handleRequest($request);

        //Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            //On récupère l'utilisateur par son email
            $user = $userRepository->findOneByEmail($form->get('email')->getData());

            //Si l'utilisateur existe on lui genère un token UNIQUE
            if ($user) {
                $token = $token->generateToken();
                //On enregistre le token dans la base de données
                $user->setResetToken($token);
                $em->persist($user);
                $em->flush();

                //on genère une url avec le token
                $url = $this->generateUrl('reset_password_token', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                //On crée les données du mail
                $context = compact('url', 'user');

                //On crée et envoi le mail

                $email = (new TemplatedEmail())
                    ->from('no-reply@meros-production.fr')
                    ->to($context['user']->getEmail())
                    ->subject('Réinitialisation de votre mot de passe')
                    ->htmlTemplate('emails/password_reset.html.twig')
                    ->context($context);

                $mail->send($email);

                //On met un message flash de succès pour notifier l'utilisateur que la requête a bien été prise en compte	
                $this->addFlash('success', 'Un email de réinitialisation de mot de passe vous a été envoyé');
                $this->redirectToRoute('login');
            } else {
                //Si l'utilisateur n'existe pas on met un message flash d'erreur
                $this->addFlash('danger', 'Cette adresse email n\'existe pas');

                return $this->redirectToRoute('login');
            }
            return $this->redirectToRoute('login');
        }

        return $this->render('login/reset_password_request.html.twig', [
            'requestPassForm' => $form->createView(),
        ]);
    }

    #[Route('/reset-password/{token}', name: 'reset_password_token')]
    public function resetPasswordToken(
        string $token,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): Response {
        // On verifie si le token est valide et existe
        $user = $userRepository->findOneBy(['resetToken' => $token]);

        //Si le token est valide on affiche le formulaire de réinitialisation de mot de passe
        //cf. src/Form/ResetPasswordType.php
        if ($user) {
            $form = $this->createForm(ResetPasswordType::class);

            $form->handleRequest($request);

            //Si le formulaire est soumis et valide
            //Cela veut dire que l'utilisateur a bien rentré un nouveau mot de passe
            if ($form->isSubmitted() && $form->isValid()) {
                //On réinitialise le token avec un champ vide
                $user->setResetToken('');
                //On hache le nouveau mot de passe pour le stocker en base de données
                $user->setPassword($hasher->hashPassword($user, $form->get('password')->getData()));
                $em->persist($user);
                $em->flush();

                //On notifie l'utilisateur que son mot de passe a bien été modifié
                //Et on le redirige vers la page de connexion
                $this->addFlash('success', 'Votre mot de passe a bien été modifié');
                return $this->redirectToRoute('login');
            }

            return $this->render('login/reset_password.html.twig', [
                'resetForm' => $form->createView(),
            ]);
        }
        //Si le jeton n'est pas valide il est au courant aussi 
        $this->addFlash('danger', 'Ce jeton n\'est pas valide');
        return $this->redirectToRoute('login');
    }
}
