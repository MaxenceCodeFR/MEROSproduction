<?php

namespace App\Controller;


use App\Entity\ContactInfluencer;
use App\Form\ContactInfluencerType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/contact/influencer', name: 'contact_influencer_')]
class ContactInfluencerController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, EntityManagerInterface $entityManager, MailerInterface $mail, UserRepository $user): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');
        $contact = new ContactInfluencer();
        $form = $this->createForm(ContactInfluencerType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setUser($this->getUser());
            // $file stores the uploaded PDF file
            $file = $form->get('cv')->getData();
            if ($file) {
                $fileName = md5(uniqid('CV_')) . '.' . $file->guessExtension();
                $contact->setCv($fileName);

                // Déplacez le fichier dans le répertoire des téléchargements
                $file->move($this->getParameter('uploads'), $fileName);
            } else {
                // Gérez le cas où aucun fichier n'est téléchargé, si nécessaire
                // Par exemple, définir $fileName à null ou à une valeur par défaut
                $contact->setCv(null);
            }

            $user = $user->findOneBy(['email' => $contact->getEmail()]);
            if ($user) {
                if ($contact->getMotif()->getId() == 1) {
                    $email = (new TemplatedEmail())
                        ->from('no-reply@meros-production.fr')
                        ->to($contact->getEmail())
                        ->subject('Votre candidature a bien été prise en compte')
                        ->htmlTemplate('emails/influencer.html.twig')
                        ->context(['user' => $user]);
                    $mail->send($email);
                } else {
                    $email = (new TemplatedEmail())
                        ->from('no-reply@meros-production.fr')
                        ->to($contact->getEmail())
                        ->subject('Votre demande a bien été prise en compte')
                        ->htmlTemplate('emails/influencer_informations.html.twig')
                        ->context(['user' => $user]);
                    $mail->send($email);
                }

                $this->addFlash('success', 'Vous avez recu un email de confirmation. Merci de votre confiance');
            }

            $entityManager->persist($contact);
            $entityManager->flush();

            return $this->redirectToRoute('contact_influencer_thankyou');
        }



        return $this->render('contact_influencer/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/thankyou', name: 'thankyou')]
    public function thankyou(): Response
    {
        return $this->render('contact_influencer/thankyou.html.twig');
    }
}
