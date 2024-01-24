<?php

namespace App\Controller;


use App\Entity\ContactCompany;
use App\Form\ContactCompanyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/contact/company', name: 'contact_company_')]
class ContactCompanyController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, EntityManagerInterface $entityManager, MailerInterface $mail): Response
    {


        $contact = new ContactCompany();
        $form = $this->createForm(ContactCompanyType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setIsDisplayed(true);

            $email = (new TemplatedEmail())
                ->from('no-reply@meros-production.fr')
                ->to($contact->getEmail())
                ->subject('Votre demande a bien été prise en compte')
                ->htmlTemplate('emails/company.html.twig')
                ->context(['contact' => $contact]);
            $mail->send($email);

            $this->addFlash('success', 'Votre demande a bien été prise en compte');

            $entityManager->persist($contact);
            $entityManager->flush();

            return $this->redirectToRoute('contact_company_thankyou');
        }

        return $this->render('contact_company/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/thankyou', name: 'thankyou')]
    public function thankyou(): Response
    {
        return $this->render('contact_company/thankyou.html.twig');
    }
}
