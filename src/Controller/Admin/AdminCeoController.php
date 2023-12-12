<?php

namespace App\Controller\Admin;


use App\Entity\Social;
use App\Form\UserType;
use App\Entity\ContactInfluencer;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ContactInfluencerRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/ceo', name: 'ceo_')]
class AdminCeoController extends AbstractController
{
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

    #[Route('/candidate', name: 'candidate')]
    public function candidate(ContactInfluencerRepository $contacts, PaginatorInterface $paginatorInterface, Request $request): Response
    {
        $data = $contacts->findCandidate(1);
        $contacts = $paginatorInterface->paginate(
            $data,
            $request->query->getInt('page', 1),
            15
        );
        return $this->render('ceo/candidates/candidate.html.twig', compact('contacts'));
    }

    #[Route('/candidate/{id}', name: 'candidate_show')]
    public function candidateShow(ContactInfluencer $candidate): Response
    {

        return $this->render('ceo/candidates/show.html.twig', compact('candidate'));
    }

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

    #[Route('/profil', name: 'profil')]
    public function profile(): Response
    {
        $user = $this->getUser();
        $socials = $user->getSocial();
        $specialities = $user->getSpecialty();


        return $this->render('ceo/profil/index.html.twig', compact('socials', 'user', 'specialities'));
    }

    #[Route('/profil/edit', name: 'profil_edit')]
    public function profilEdit(Request $request, EntityManagerInterface $em): Response
    {
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
