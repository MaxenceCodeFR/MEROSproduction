<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
}
