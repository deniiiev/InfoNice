<?php

namespace App\Controller\Dashboard;

use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dash", name="dash_")
 */
class DashHomeController extends AbstractController
{
    /**
     * @Route("/", name="home_index")
     * @param PostRepository $postRepository
     * @param CategoryRepository $categoryRepo
     * @return Response
     */
    public function index(PostRepository $postRepository, CategoryRepository $categoryRepo): Response
    {
        $categories = $categoryRepo->findAll();
        $posts = $postRepository->findBy([],['publishedAt' => 'DESC']);
        return $this->render('dashboard/home/index.html.twig', [
            'posts' => $posts,
            'categories' => $categories
        ]);
    }
}
