<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Service\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function profile(User $user, PostRepository $postRepo): Response
    {
        if ($user == $this->getUser() || $this->isGranted('ROLE_ADMIN')) {
            $posts = $postRepo->findBy(['author'=>$user],['createdAt' => 'DESC']);
        } else {
            $posts = $postRepo->findBy(['author'=>$user, 'published' => true],['createdAt' => 'DESC']);
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/bookmarks/{page<\d+>?1}", name="user_bookmarks")
     * @param $page
     * @param Paginator $paginator
     * @param PostRepository $postRepo
     * @return Response
     */
    public function bookmarks($page, Paginator $paginator, PostRepository $postRepo): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $this->getUser()->getUsername()]);

        $paginator
            ->setClass(Post::class)
            ->setType('post')
            ->setOrder(['addedAt' => 'DESC'])
            ->setType('bookmark')
            ->setCriteria(['user' => $user,'published' => true])
            ->setLimit(10)
            ->setPage($page)
        ;

        return $this->render('user/bookmarks.html.twig', [
            'posts' => $paginator->getData(),
            'paginator' => $paginator
        ]);
    }

    /**
     * @Route ("/settings", name="user_settings", methods={"GET","POST"})
     * @param Request $request
     * @param UserRepository $repo
     * @return Response
     */
    public function settings(Request $request, UserRepository $repo): Response
    {
        $user = $repo->findOneBy(['username'=>$this->getUser()->getUsername()]);
        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_profile', [
                'username' => $user->getUsername()
            ]);
        }

        return $this->render('user/settings.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
}
