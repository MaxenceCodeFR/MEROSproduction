<?php

namespace App\Controller\Influencer;

use App\Entity\User;
use App\Entity\Media;
use App\Entity\Social;
use App\Form\UserType;
use App\Form\InfluencerType;
use App\Repository\UserRepository;
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

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('image')->getData();
            if ($file) {
                $fileName = md5(uniqid('IMG_')) . '.' . $file->guessExtension();
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
}
