<?php

namespace App\Controller;


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
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');
        $contact = new ContactInfluencer();
        $form = $this->createForm(ContactInfluencerType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setUser($this->getUser());
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
