<?php

namespace App\Controller;

use App\Form\ResetPasswordType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use App\Form\ResetPasswordRequestType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
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

    #[Route('/reset-password', name: 'reset_password')]
    public function resetPassword(
        Request $request,
        UserRepository $userRepository,
        TokenGeneratorInterface $token,
        EntityManagerInterface $em,
        MailerInterface $mail
    ): Response {
        $form = $this->createForm(ResetPasswordRequestType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneByEmail($form->get('email')->getData());

            if ($user) {
                $token = $token->generateToken();
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

                $this->addFlash('success', 'Un email de réinitialisation de mot de passe vous a été envoyé');
                $this->redirectToRoute('login');
            } else {
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
        // On verifie si le token est valide
        $user = $userRepository->findOneBy(['resetToken' => $token]);

        if ($user) {
            $form = $this->createForm(ResetPasswordType::class);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $user->setResetToken('');
                $user->setPassword($hasher->hashPassword($user, $form->get('password')->getData()));
                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Votre mot de passe a bien été modifié');
                return $this->redirectToRoute('login');
            }

            return $this->render('login/reset_password.html.twig', [
                'resetForm' => $form->createView(),
            ]);
        }
        $this->addFlash('danger', 'Ce jeton n\'est pas valide');
        return $this->redirectToRoute('login');
    }

    #[Route('/reset-error', name: 'reset_error')]
    public function resetError(): Response
    {
        return $this->render('login/errorreset.html.twig');
    }
}
