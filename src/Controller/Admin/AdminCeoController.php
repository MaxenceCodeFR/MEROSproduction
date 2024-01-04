<?php

namespace App\Controller\Admin;

use App\Entity\Social;
use App\Form\UserType;
use App\Entity\ContactCompany;
use App\Entity\ContactInfluencer;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\ContactCompanyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ContactInfluencerRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/ceo', name: 'ceo_')]
class AdminCeoController extends AbstractController
{
    //////////////////////////////////////////////////////////////////////
    /////AFFICHAGE DE L'ACCUEIL DE CEO////////////////////////////////////
    //////////////////////////////////////////////////////////////////////
    #[Route('/', name: 'index')]
    public function index(): Response
    {

        // $users = $paginatorInterface->paginate(
        //     $userRepository,
        //     $request->query->getInt('page', 1),
        //     8
        // );
        // var_dump($users);
        return $this->render('ceo/index.html.twig');
    }

    //////////////////////////////////////////////////////////////////////
    /////PARTIES "CANDIDATURES INFLUENCEURS" et "DEMANDES"////////////////
    //////////////////////////////////////////////////////////////////////
    //Affichage des candidatures
    #[Route('/candidate', name: 'candidate')]
    public function candidate(ContactInfluencerRepository $candidates, PaginatorInterface $paginatorInterface, Request $request): Response
    {
        $data = $candidates->findCandidate(1);
        $candidates = $paginatorInterface->paginate(
            $data,
            $request->query->getInt('page', 1),
            15
        );
        return $this->render('ceo/candidates/candidate.html.twig', compact('candidates'));
    }
    //Affichage d'un candidat en détail
    #[Route('/candidate/{id}', name: 'candidate_show')]
    public function candidateShow(ContactInfluencer $candidate): Response
    {

        return $this->render('ceo/candidates/show.html.twig', compact('candidate'));
    }
    //BOUTON POUR PASSER UN CANDIDAT EN INFLUENCEUR
    #[Route('/set-influencer/{id}', name: 'set_influencer')]
    function setInfluencer(Request $request, EntityManagerInterface $em, ContactInfluencerRepository $candidate): Response
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
    public function deleteCandidate(Request $request, EntityManagerInterface $em, ContactInfluencerRepository $candidateRepo, ContactInfluencer $contact): Response
    {
        // Récupération du candidat via son ID
        $candidate = $candidateRepo->find($request->get('id'));

        if ($candidate) {
            // Supprimer la candidature
            $em->remove($candidate);
            $em->flush();

            // Ajouter un message flash ou autre logique si nécessaire
        }
        // Si le motif est "Demande d'informations"(id 2 des différents motids), on redirige vers la page des demandes d'informations
        if ($contact->getMotif(2)) {
            return $this->redirectToRoute('ceo_request');
        } else {
            return $this->redirectToRoute('ceo_candidate');
        }
    }

    //DEMANDES D'INFORMATIONS
    #[Route('/request', name: 'request')]
    public function request(ContactInfluencerRepository $contacts, PaginatorInterface $paginatorInterface, Request $request): Response
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
        return $this->render('ceo/company/company.html.twig', compact('company'));
    }
    //Affichage d'une demande d'entreprise en détail
    #[Route('/company/{id}', name: 'company_show')]
    public function companyShow(ContactCompany $company)
    {
        return $this->render('ceo/company/show.html.twig', compact('company'));
    }
    //Suppression d'une demande d'entreprise
    //Ce n'est pas vraiment une suppression, on garde les données donc on affiche ou pas en fonction du besoin
    #[Route('/company/{id}/delete', name: 'company_delete')]
    public function companyDelete(ContactCompany $company, EntityManagerInterface $em)
    {
        $company->setIsDisplayed(false);
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
}
//////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
