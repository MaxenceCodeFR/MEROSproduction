<?php

namespace App\Controller;


use App\Entity\Notification;
use App\Entity\ContactCompany;
use App\Form\ContactCompanyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\EmailService;

#[Route('/contact/company', name: 'contact_company_')]
class ContactCompanyController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        Request $request, 
        EntityManagerInterface $entityManager, 
        EmailService $emailService): Response
    {


        $contact = new ContactCompany();
        $form = $this->createForm(ContactCompanyType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //*afficher le formulaire lors de sa création 
            $contact->setIsDisplayed(true);

            //*generer une notification lors de sa création
            $notification = new Notification();
            $notification->setIsNew(true);
            $notification->setIsSeen(false);
            $contact->setNotification($notification);
            
            //! voir  'src/Service/EmailService.php'
            $emailService->sendEmailFromNoReply(
                $contact->getEmail(),
                'Votre demande a bien été prise en compte',
                'emails/company.html.twig',
                ['contact' => $contact]
            );
            
            $this->addFlash('success', 'Votre demande a bien été prise en compte');
            
            $entityManager->persist($notification);
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
