<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{
    /**
     * @Route("/user/{username}", name="user_profile")
     * @param User $user
     * @param PostRepository $postRepo
     * @return Response
     */
    public function home(User $user, PostRepository $postRepo): Response
    {
        $posts = $postRepo->findBy(['author'=>$user],['createdAt' => 'DESC']);
        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'posts' => $posts
        ]);
    }

}
