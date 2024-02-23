<?php

namespace App\Controller\Admin;

use App\Entity\Blog;
use App\Form\BlogType;
use App\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/editor-blog', name: 'editor_')]
class AdminBlogController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        BlogRepository $blogRepository, 
        PaginatorInterface $paginatorInterface, 
        Request $request): Response
    {
        //Récupération des articles non archivés via la méthode findAll() du repository
        //Cette méthode est modifiée dans le repository pour ne récupérer que les articles non archivés
        //cf. BlogRepository.php => findAll()
        $data = $blogRepository->findAll();

        //On utilise le service KNP Paginator pour paginer les articles :
        //Le premier paramètre nous demande les données à paginer
        //Le deuxième paramètre nous demande le nom de la page dans le fichier "knp_paginator.yaml"
        // et la page par défaut
        //Le troisème paramètre et le nombre d'articles par page 
        $blogs = $paginatorInterface->paginate(
            $data,
            $request->query->getInt('page', 1),
            8
        );

        $keyword = $request->query->get('keyword');

        if ($keyword) {
            $results = $blogRepository->searchByTitle($keyword);
        } else {
            // Gérer le cas où aucun mot-clé n'est saisi
            $results = [];
        }

        return $this->render('blog/admin/blog_editor/index.html.twig', compact('blogs', 'results'));
    }

    #[Route('/editor-archived', name: 'archived')]
    public function archived(
        BlogRepository $blogRepository, 
        PaginatorInterface $paginatorInterface, 
        Request $request): Response
    {
        //Récupération des articles archivés via la méthode findBy() du repository
        //Cette méthode est native donc non-modifiée
        $data = $blogRepository->findBy(['isArchived' => true]);
        $archived = $paginatorInterface->paginate(
            $data,
            $request->query->getInt('page', 1),
            8
        );
        return $this->render('blog/admin/blog_editor/archived.html.twig', compact('archived'));
    }

    #[Route('/set-archive/{id}', name: 'set_archive')]
    public function setArchive(Request $request, EntityManagerInterface $em): Response
    {
        //Récupération de l'article à archiver via son id
        //On utilise la méthode find() du repository
        $blog = $em->getRepository(Blog::class)->find($request->get('id'));
        $blog->setIsArchived(true);
        $em->flush();

        return $this->redirectToRoute('editor_archived');
    }


    #[Route('/archived/{id}', name: 'archive')]
    public function showArchived(Blog $blog): Response
    {
        return $this->render('blog/show.html.twig', [
            'blog' => $blog,

        ]);
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

        $contentFromTinymce = $blog->getContent();


        if ($form->isSubmitted() && $form->isValid()) {
            $blog->setIsArchived(false);
            $blog->setCreatedAt(new \DateTimeImmutable());

            $file = $form->get('image')->getData();
            $fileName = md5(uniqid('BLOG_')) . '.' . $file->guessExtension();
            $blog->setImage($fileName);
            $file->move($this->getParameter('uploads'), $fileName);

            $em->persist($blog);
            $em->flush();

            //Ajouter une redirection vers la page de l'article
            return $this->redirectToRoute('blog_index');
        }

        return $this->render('blog/admin/add.html.twig', [
            'form' => $form->createView(),

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

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Request $request, Blog $blog, EntityManagerInterface $em): Response
    {
        $token = 'delete_' . $blog->getId();
        $submittedToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid($token, $submittedToken)) {
            $em->remove($blog);
            $em->flush();
        }

        return $this->redirectToRoute('blog_index');
    }

}
