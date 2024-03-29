<?php

namespace App\Controller\Influencer;

use App\Entity\Media;
use App\Entity\Social;
use App\Form\UserType;
use App\Entity\PromotedLink;
use App\Entity\ContactCompany;
use App\Form\PromotedLinkType;
use App\Repository\PromotedLinkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/influencer', name: 'influencer_')]
class InfluencerController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function influencerLanding(): Response
    {
        return $this->render('influencer/influencer_landing.html.twig');
    }

    #[Route('/profil', name: 'profil')]
    public function influencerProfile(): Response
    {
        $user = $this->getUser();
        $specialities = $user->getSpecialty();
        $socials = $user->getSocial();
        return $this->render('influencer/profil/influencer_profil.html.twig', compact('user', 'specialities', 'socials'));
    }

    #[Route('/edit', name: 'edit')]
    public function influencerEdit(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if ($user->getSocial()->isEmpty()) {
            $user->addSocial(new Social());
        }

        $form = $this->createForm(UserType::class, $user, ['isCEO' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('image')->getData();
            if ($file) {
                $fileName = 'INFLU'. '_' . md5(uniqid()) . '.' . $file->guessExtension();
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

            $this->addFlash('success', 'Votre profil a bien été modifié');

            return $this->redirectToRoute('influencer_profil');
        }

        return $this->render('influencer/profil/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/contracts', name: 'contracts')]
    public function influencerContracts(): Response
    {
        $user = $this->getUser();
        $user->getContactCompanies();

        return $this->render('influencer/influencer_contracts.html.twig', compact('user'));
    }

    #[Route('/contract/{id}', name: 'contract')]
    public function influencerContract(ContactCompany $contact): Response
    {
        return $this->render('influencer/influencer_contract.html.twig', compact('contact'));
    }

//* Ajout des liens de promotion
    #[Route('/add-promoted-links', name: 'add_promoted_links')]
    public function addPromotedLinks(
        Request $request,
        EntityManagerInterface $em
        ): Response
    {
        $user = $this->getUser(); // Récupérer l'utilisateur connecté
        // Compter le nombre de liens promus existants pour cet utilisateur
        $countPromotedLinks = $em->getRepository(PromotedLink::class)->count(['user' => $user]);

        $promotedLink = new PromotedLink(); // Créer une nouvelle instance de PromotedLink
        $form = $this->createForm(PromotedLinkType::class, $promotedLink); // Créer le formulaire
        $form->handleRequest($request); // Gérer la requête

        if ($form->isSubmitted() && $form->isValid()) {
            if ($countPromotedLinks >= 2) {
                $this->addFlash('danger', 'Vous ne pouvez pas ajouter plus de 2 liens promus.');
                // Vous pouvez choisir de renvoyer la vue avec le formulaire et le message d'erreur
                // ou de rediriger l'utilisateur vers une autre route
                return $this->redirectToRoute('influencer_promoted_links');
            }
            $promotedLink->setUser($user); // Associer l'utilisateur à la promotion
            $em->persist($promotedLink); // Persister la promotion
            $em->flush(); // Exécuter la requête

            $this->addFlash('success', 'Votre lien a bien été ajouté'); // Ajouter un message flash

        }
        return $this->render('influencer/add_promoted_links.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/promoted-links', name: 'promoted_links')]
    public function view(PromotedLinkRepository $promoted): Response
    {
        $promoted = $promoted->findBy(['user' => $this->getUser()]);

        return $this->render('influencer/promoted_links.html.twig', ['promoted' => $promoted]);
    }
    #[Route('/edit-promoted-links/{id}', name: 'edit_promoted_links')]
    public function editPromotedLinks(PromotedLink $promoted, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PromotedLinkType::class, $promoted);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($promoted);
            $em->flush();

            $this->addFlash('success', 'Le lien a bien été modifié');

            return $this->redirectToRoute('influencer_promoted_links');
        }

        return $this->render('influencer/edit_promoted_links.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete-promoted-links/{id}', name: 'delete_promoted_links')]
    public function delete(PromotedLink $promoted, EntityManagerInterface $em): Response
    {
        $em->remove($promoted);
        $em->flush();

        $this->addFlash('success', 'Le lien a bien été supprimé');

        return $this->redirectToRoute('influencer_edit_promoted_links');
    }
}
