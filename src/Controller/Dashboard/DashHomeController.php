<?php

namespace App\Controller\Dashboard;

use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
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
     * @param PostRepository $postRepo
     * @param CategoryRepository $categoryRepo
     * @param UserRepository $userRepo
     * @param CommentRepository $commentRepo
     * @return Response
     */
    public function index(PostRepository $postRepo, CategoryRepository $categoryRepo, UserRepository $userRepo, CommentRepository $commentRepo): Response
    {
        $moderation = [
            'post_moderation' => [
                'path' => 'dash_moderation_posts',
                'count' => $postRepo->count(['published' => null])
            ],
            'comment_moderation' => [
                'path' => 'dash_moderation_comments',
                'count' => count($commentRepo->complaintsMoreThan(10))
            ]
        ];

        $informations = [
            'users' => $userRepo->count([]),
            'categories' => $categoryRepo->count([]),
            'news' => $postRepo->count(['section' => 'news']),
            'ads' =>  $postRepo->count(['section' => 'ads']),
            'questions' => $postRepo->count(['section' => 'questions']),
        ];

        return $this->render('dashboard/home/index.html.twig', [
            'informations' => $informations,
            'moderation' => $moderation
        ]);
    }
}
