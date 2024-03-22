<?php

namespace App\Controller\Admin;

use App\Entity\Calendar;
use App\Entity\User;
use App\Entity\Media;
use App\Entity\Social;
use App\Form\CalendarType;
use App\Form\UserType;
use App\Entity\ContactCompany;
use App\Entity\ContactInfluencer;
use App\Repository\UserRepository;
use App\Service\ManageNotification;
use App\Form\AffiliateInfluencerType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\ContactCompanyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ContactInfluencerRepository;
use App\Repository\NotificationRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/ceo', name: 'ceo_')]
class AdminCeoController extends AbstractController
{
    //////////////////////////////////////////////////////////////////////
    /////AFFICHAGE DE L'ACCUEIL DE CEO////////////////////////////////////
    //////////////////////////////////////////////////////////////////////
    #[Route('/', name: 'index')]
    public function index(NotificationRepository $notificationRepository): Response
    {
        
        $companyNotificationsCount = 0;
        $influencerNotificationsCount = 0;
    
        $notifications = $notificationRepository->findBy(['isNew' => true, 'isSeen' => false]);
    
        foreach ($notifications as $notification) {
            $companyNotificationsCount += count($notification->getContactCompanies());
            $influencerNotificationsCount += count($notification->getContactInfluencers());
        }
        
        return $this->render('ceo/index.html.twig', compact('companyNotificationsCount', 'influencerNotificationsCount'));
    }

    //////////////////////////////////////////////////////////////////////
    /////PARTIES "CANDIDATURES INFLUENCEURS" et "DEMANDES"////////////////
    //////////////////////////////////////////////////////////////////////
    //Affichage des candidatures
    #[Route('/candidate', name: 'candidate')]
    public function candidate(
        ContactInfluencerRepository $candidates,
        PaginatorInterface $paginatorInterface,
        Request $request): Response
    {
        // $data = $candidates->findCandidate(1);
        // $candidates = $paginatorInterface->paginate(
        //     $data,
        //     $request->query->getInt('page', 1),
        //     15
        // );
        $candidates = $candidates->findAllCandidatesByNewest();
        return $this->render('ceo/candidates/candidate.html.twig', compact('candidates'));
    }
    //Affichage d'un candidat en détail
    #[Route('/candidate/{id}', name: 'candidate_show')]
    public function candidateShow(
        ContactInfluencer $candidate, 
        ManageNotification $manageNotification): Response
    {

        $manageNotification->updateNotificationStatus($candidate);
        return $this->render('ceo/candidates/show.html.twig', compact('candidate'));
    }
    //BOUTON POUR PASSER UN CANDIDAT EN INFLUENCEUR
    #[Route('/set-influencer/{id}', name: 'set_influencer')]
    function setInfluencer(
        Request $request, 
        EntityManagerInterface $em, 
        ContactInfluencerRepository $candidate): Response
    {
        // Récupération du candidat via son ID
        $candidate = $candidate->find($request->get('id'));

        if ($candidate) {
            // Récupération de la personne liée au candidat (supposons que c'est un utilisateur)
            $user = $candidate->getUser();

            if ($user) {
                // Assigner le rôle 'ROLE_INFLUENCER' à l'utilisateur
                $user->setRoles(['ROLE_INFLUENCER']);
                $em->flush();
            }
        }

        return $this->redirectToRoute('ceo_candidate');
    }

    //BOUTON POUR PASSER UN CANDIDAT EN CANDIDAT REFUSE
    #[Route('/delete-candidate/{id}', name: 'candidate_delete')]
    public function deleteCandidate(
        Request $request, 
        EntityManagerInterface $em, 
        ContactInfluencerRepository $candidateRepo, 
        ContactInfluencer $contact): Response
    {
        // Récupération du candidat via son ID
        $candidate = $candidateRepo->find($request->get('id'));

        if ($candidate) {
            // Supprimer la candidature
            $em->remove($candidate);
            $em->flush();
        }
        // Si le motif est "Demande d'informations"(id 2 des différents motifs), on redirige vers la page des demandes d'informations
        if ($contact->getMotif(2)) {
            return $this->redirectToRoute('ceo_request');
        } else {
            return $this->redirectToRoute('ceo_candidate');
        }
    }

    //DEMANDES D'INFORMATIONS
    #[Route('/request', name: 'request')]
    public function request(
        ContactInfluencerRepository $contacts, 
        PaginatorInterface $paginatorInterface, 
        Request $request): Response
    {
        $data = $contacts->findCandidate(2);
        $contacts = $paginatorInterface->paginate(
            $data,
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('ceo/request/request.html.twig', compact('contacts'));
    }
    //////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////

    //////////////////////////////////////////////////////////////////////
    /////PARTIES "DEMANDES DES ENTREPRISES"//////////////////////////////
    //////////////////////////////////////////////////////////////////////
    //Affichage des demandes des entreprises
    #[Route('/company', name: 'company')]
    public function company(ContactCompanyRepository $company)
    {
        $company = $company->findAll();
        //dd($company);
        return $this->render('ceo/company/company.html.twig', compact('company'));
    }
    //Affichage d'une demande d'entreprise en détail
    #[Route('/company/{id}', name: 'company_show')]
    public function companyShow(
        ContactCompany $company,
        EntityManagerInterface $em,
        Request $request,
        ManageNotification $manageNotification): Response
    {
        //cf src/Service/manageNotification.php
        $manageNotification->updateNotificationStatus($company);

        $calendar = new Calendar();
        $calendar->setTitle($company->getCompany());
        $startDate = $company->getStart() ?? new \DateTime(); // Utilisation de l'opérateur null coalescent
        $endDate = $company->getEnd() ?? new \DateTime();
        $calendar->setStart($startDate);
        $calendar->setEnd($endDate);
        $calendar->setBackgroundColor('#FFFFFF');
        $calendar->setBorderColor('#FFFFFF');
        $calendar->setTextColor('#5AC432');

// Création et traitement du formulaire
        $form = $this->createForm(CalendarType::class, $calendar, [
            'include_color_options' => false
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->get('user')->getData();
            $company->setUser($user);

            // Mettre à jour $company avec les dates de $calendar issues du formulaire
            $company->setStart($calendar->getStart());
            $company->setEnd($calendar->getEnd());

            // Persister les entités $calendar et $company
            $em->persist($calendar);
            $em->persist($company);
            $em->flush();

            $this->addFlash('success', 'La demande a bien été traitée');
            return $this->redirectToRoute('ceo_company');
        }

        return $this->render('ceo/company/show.html.twig', [
            'company' => $company,
            'form' => $form->createView(),
        ]);
    }
    //Suppression d'une demande d'entreprise
    //Ce n'est pas vraiment une suppression, on garde les données donc on affiche ou pas en fonction du besoin
    #[Route('/company/{id}/delete', name: 'company_delete')]
    public function companyDelete(ContactCompany $company, EntityManagerInterface $em, ManageNotification $manageNotification): Response
    {
        $company->setIsDisplayed(false);
        if ($company->isIsDisplayed() === false) {
            $manageNotification->updateNotificationStatus($company);
        }
        $em->persist($company);
        $em->flush();
        return $this->redirectToRoute('ceo_company');
    }
    //////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////


    //////////////////////////////////////////////////////////////////////
    /////PARTIES "PROFILS DES CEO"///////////////////////////////////////
    //////////////////////////////////////////////////////////////////////
    //Affichage du profil du CEO
    #[Route('/profil', name: 'profil')]
    public function profile(): Response
    {
        $user = $this->getUser();
        $socials = $user->getSocial();
        $specialities = $user->getSpecialty();

        return $this->render('ceo/profil/index.html.twig', compact('socials', 'user', 'specialities'));
    }

    //Modification du profil du CEO
    #[Route('/profil/edit', name: 'profil_edit')]
    public function profilEdit(Request $request, EntityManagerInterface $em): Response
    {
        //On récupère l'utilisateur connecté
        $user = $this->getUser();

        //Si l'utilisateur n'a pas de social, on lui en crée un
        if ($user->getSocial()->isEmpty()) {
            $user->addSocial(new Social());
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('image')->getData();
            if ($file) {
                $fileName = md5(uniqid('CEO_')) . '.' . $file->guessExtension();
                $file->move($this->getParameter('uploads'), $fileName);

                // Trouver les anciennes images de l'utilisateur
                $oldMedias = $em->getRepository(Media::class)->findByUser($user);

                // Supprimer les anciennes images
                foreach ($oldMedias as $oldMedia) {
                    $em->remove($oldMedia);
                }

                // Créer une nouvelle instance de Media
                $media = new Media();
                $media->setImages($fileName); // Définir l'image
                $media->setUser($user); // Associer l'utilisateur
                $em->persist($media);
                $em->flush();
            }

            //On boucle sur les socials de l'utilisateur
            foreach ($user->getSocial() as $social) {
                // Persistez seulement les nouvelles entités Social
                if (!$em->contains($social)) {
                    $em->persist($social);
                }
            }

            $em->persist($this->getUser());
            $em->flush();

            return $this->redirectToRoute('ceo_profil');
        }

        return $this->render('ceo/profil/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    //////////////////////////////////////////////////////////////////////
    /////PARTIES "GESTION DES INFLUENCEURS"////////////////////////////////


    #[Route('/manage-influencer', name: 'manage_influencer')]
    public function manageInfluencer(UserRepository $userRepository, Request $request):Response
    {
        $keyword = $request->query->get('keyword');

        if ($keyword) {
            $results = $userRepository->searchInfluencer($keyword);
        } else {
            $results = [];
        }
        
        $influencers = $userRepository->findRoleInfluencer();

        return $this->render('ceo/influencer/manage_influencer.html.twig', compact('influencers', 'results'));
    }

    #[Route('/manage-influencer/{id}', name:'influencer_show')]
    public function influencerShow(User $influencer):Response
    {
        return $this->render('ceo/influencer/show.html.twig', compact('influencer'));
    }

    #[Route('/manage-influencer/{id}/edit', name:'influencer_edit')]
public function influencerEdit(Request $request, EntityManagerInterface $em, User $influencer): Response
{
    // Créez le formulaire en passant l'entité de l'influenceur
    $form = $this->createForm(UserType::class, $influencer);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $file = $form->get('image')->getData();
        if ($file) {
            $fileName = md5(uniqid('IMG_')) . '.' . $file->guessExtension();
            $file->move($this->getParameter('uploads'), $fileName);

            // Créer une nouvelle instance de Media si nécessaire
            $media = $influencer->getImages() ?? new Media();
            $media->setImages($fileName); // Définir l'image
            $em->persist($media);

            // Associer le media à l'influenceur si nécessaire
            $influencer->addImage($media);
        }

        // Enregistrez les modifications de l'influenceur
        $em->persist($influencer);
        $em->flush();

        // Rediriger ou afficher un message de succès après l'enregistrement
        $this->addFlash('success','L\'influenceur a bien été modifié');
        return $this->redirectToRoute('ceo_manage_influencer');
    }

    return $this->render('ceo/influencer/edit.html.twig', ['form'=> $form->createView()]);
}

}
//////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
