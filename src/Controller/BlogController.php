<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use Doctrine\ORM\EntityManager;
use App\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/blog', name: 'blog_')]
class BlogController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(BlogRepository $blogRepository): Response
    {
        //Récupération des articles non archivés via la méthode findAll() du repository
        //Cette méthode est modifiée dans le repository pour ne récupérer que les articles non archivés
        //cf. BlogRepository.php => findAll()
        $blogs = $blogRepository->findAll();
        return $this->render('blog/index.html.twig', compact('blogs'));
    }

    #[Route('/archived', name: 'archived')]
    public function archived(BlogRepository $blogRepository): Response
    {
        //Récupération des articles archivés via la méthode findBy() du repository
        //Cette méthode est native donc non-modifiée
        $archived = $blogRepository->findBy(['isArchived' => true]);
        return $this->render('blog/archived.html.twig', compact('archived'));
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Blog $blog): Response
    {
        return $this->render('blog/show.html.twig', [
            'blog' => $blog,

        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {

        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);



        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $blog->setIsArchived(false);
            $blog->setCreatedAt(new \DateTimeImmutable());

            $file = $form->get('image')->getData();
            $fileName = md5(uniqid('IMG_')) . '.' . $file->guessExtension();
            $blog->setImage($fileName);
            $file->move($this->getParameter('uploads'), $fileName);

            $em->persist($blog);
            $em->flush();

            //Ajouter une redirection vers la page de l'article
            return $this->redirectToRoute('blog_index');
        }

        return $this->render('blog/admin/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Blog $blog, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BlogType::class, $blog);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('image')->getData();
            if ($file) {
                $fileName = md5(uniqid('IMG_')) . '.' . $file->guessExtension();
                $blog->setImage($fileName);
                $file->move($this->getParameter('uploads'), $fileName);
            }

            $em->flush();

            //Ajouter une redirection vers la page de l'article
            return $this->redirectToRoute('blog_index');
        }

        return $this->render('blog/admin/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
