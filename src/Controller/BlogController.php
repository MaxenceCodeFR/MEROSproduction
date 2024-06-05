<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use App\Repository\BlogRepository;
use App\Service\BreadcrumbService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/blog', name: 'blog_')]
class BlogController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        BlogRepository $blogRepository,
        PaginatorInterface $paginatorInterface,
        BreadcrumbService $breadcrumbService,
        Request $request): Response
    {
        $breadcrumbService->add('Accueil', $this->generateUrl('landing'));
        $breadcrumbService->add('Blog', $this->generateUrl('blog_index'));
        $data = $blogRepository->findAllArticlesByDates();
        $blogs = $paginatorInterface->paginate(
            $data,
            $request->query->getInt('page', 1),
            8
        );

        $keyword = $request->query->get('keyword');
        $results = [];

        if ($keyword) {
            $results = $blogRepository->searchByTitle($keyword);
            // Check if the results array is empty and add a flash message if so
            if (empty($results)) {
                $this->addFlash('warning', 'Aucun article trouvé pour le mot-clé recherché.');
            }
        } else {
            // Optionally handle the case where no keyword is entered
            $this->addFlash('info', 'Entrez un mot-clé pour rechercher des articles.');
        }
        
        $paramaters = [
            'blogs' => $blogs,
            'results' => $results,
            'keyword' => $keyword,
            'breadcrumbs' => $breadcrumbService->getBreadcrumbs()
        ];

        return $this->render('blog/index.html.twig', $paramaters);
    }



    #[Route('/archived', name: 'archived')]
    public function archived(BlogRepository $blogRepository, BreadcrumbService $breadcrumbService): Response
    {
        $breadcrumbService->add('Accueil', $this->generateUrl('landing'));
        $breadcrumbService->add('Articles archivés', $this->generateUrl('blog_archived'));
        //Récupération des articles archivés via la méthode findBy() du repository
        //Cette méthode est native donc non-modifiée
        $archived = $blogRepository->findBy(['isArchived' => true]);

        $parameters = [
            'archived' => $archived,
            'breadcrumbs' => $breadcrumbService->getBreadcrumbs()
        ];
        return $this->render('blog/archived.html.twig', $parameters);
    }

    #[Route('/archived/{id}', name: 'archive')]
    public function showArchived(Blog $blog, BreadcrumbService $breadcrumbService): Response
    {
        $breadcrumbService->add('Accueil', $this->generateUrl('landing'));
        $breadcrumbService->add('Articles archivés', $this->generateUrl('blog_archived'));
        $breadcrumbService->add($blog->getTitle(), $this->generateUrl('blog_show', ['id' => $blog->getId()]));

        $parameters = [
            'blog' => $blog,
            'breadcrumbs' => $breadcrumbService->getBreadcrumbs()
        ];

        return $this->render('blog/showArchived.html.twig', $parameters);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Blog $blog, BreadcrumbService $breadcrumbService): Response
    {
        $breadcrumbService->add('Accueil', $this->generateUrl('landing'));
        $breadcrumbService->add('Blog', $this->generateUrl('blog_index'));
        $breadcrumbService->add($blog->getTitle(), $this->generateUrl('blog_show', ['id' => $blog->getId()]));

        $parameters = [
            'blog' => $blog,
            'breadcrumbs' => $breadcrumbService->getBreadcrumbs()
        ];
        return $this->render('blog/show.html.twig', $parameters);
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
            $fileName = md5(uniqid('IMG_')) . '.' . $file->guessExtension();
            $blog->setImage($fileName);
            $file->move($this->getParameter('uploads'), $fileName);

            $em->persist($blog);
            $em->flush();

            //Ajouter une redirection vers la page de l'article
            return $this->redirectToRoute('editor_index');
        }

        return $this->render('blog/admin/add.html.twig', [
            'form' => $form->createView(),

        ]);
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Blog $blog, 
        EntityManagerInterface $em): Response
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
            return $this->redirectToRoute('editor_index');
        }

        return $this->render('blog/admin/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(
    Request $request,
    Blog $blog,
    EntityManagerInterface $em): Response
    {
        $token = 'delete_' . $blog->getId();
        $submittedToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid($token, $submittedToken)) {
            $em->remove($blog);
            $em->flush();
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('editor_index');
        }
        return $this->redirectToRoute('blog_index');
    }
}
