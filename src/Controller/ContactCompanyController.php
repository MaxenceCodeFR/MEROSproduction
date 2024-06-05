<?php

namespace App\Controller;


use App\Entity\Notification;
use App\Entity\ContactCompany;
use App\Form\ContactCompanyType;
use App\Service\BreadcrumbService;
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
        EmailService $emailService,
        BreadcrumbService $breadcrumbService): Response
    {

        $breadcrumbService->add('Accueil', $this->generateUrl('landing'));
        $breadcrumbService->add('Contact entreprise', $this->generateUrl('contact_company_index'));
        //Créer une nouvelle instance de l'entité ContactCompany
        $contact = new ContactCompany();
        //Créer un formulaire à partir de l'entité ContactCompany et le stocké dans la variable $form
        $form = $this->createForm(ContactCompanyType::class, $contact);
        //Récupérer les données de la requête
        $form->handleRequest($request);
        //Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            //*afficher le formulaire lors de sa création 
            $contact->setIsDisplayed(true);

            //*generer une notification lors de sa création
            $notification = new Notification();
            $notification->setIsNew(true);
            $notification->setIsSeen(false);
            $contact->setNotification($notification);
            
            // voir  'src/Service/EmailService.php'
            $emailService->sendEmailFromNoReply(
                $contact->getEmail(),
                'Votre demande a bien été prise en compte',
                'emails/company.html.twig',
                ['contact' => $contact]
            );
            //Ajouter un message flash de succès
            $this->addFlash('success', 'Votre demande a bien été prise en compte');

            //Mise en attente par l'ORM de $notification et $contact
            $entityManager->persist($notification);
            $entityManager->persist($contact);
            //Ecriture en base de données
            $entityManager->flush();

            //Rediriger l'utilisateur vers la page de remerciement
            return $this->redirectToRoute('contact_company_thankyou');
        }
        $parameters = [
            'form' => $form->createView(),
            'breadcrumbs' => $breadcrumbService->getBreadcrumbs()
        ];
        //Afficher le formulaire
        return $this->render('contact_company/index.html.twig', $parameters);
    }

    #[Route('/thankyou', name: 'thankyou')]
    public function thankyou(): Response
    {
        return $this->render('contact_company/thankyou.html.twig');
    }
}
