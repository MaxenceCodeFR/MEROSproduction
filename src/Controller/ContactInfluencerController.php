<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\ContactInfluencer;
use App\Form\ContactInfluencerType;
use App\Repository\ContactInfluencerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/contact/influencer', name: 'contact_influencer_')]
class ContactInfluencerController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, EntityManagerInterface $entityManager, ContactInfluencerRepository $contactInfluencerRepository): Response
    {

        $contact = new ContactInfluencer();
        $form = $this->createForm(ContactInfluencerType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $submittedEmail = $contact->getEmail();

            // Vérifier si l'email existe déjà dans la base de données
            $existingContact = $contactInfluencerRepository->findOneBy(['email' => $submittedEmail]);

            if ($existingContact) {
                // Rediriger si l'email existe déjà dans la base de données
                return $this->redirectToRoute('contact_influencer_thankyou');
            }
            // $file stores the uploaded PDF file
            $file = $form->get('cv')->getData();
            $fileName = md5(uniqid('CV_')) . '.' . $file->guessExtension();
            $contact->setCv($fileName);
            $file->move($this->getParameter('uploads'), $fileName);

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
