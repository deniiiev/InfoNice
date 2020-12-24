<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Post;
use App\Entity\User;
use App\Form\ProfileType;
use App\Form\UserType;
use App\Repository\NotificationRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Service\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\UploadHandler;


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
     * @Route("/notifications/{page<\d+>?1}", name="user_notifications")
     * @param $page
     * @param NotificationRepository $notifyRepo
     * @param UserRepository $userRepo
     * @param Paginator $paginator
     * @return Response
     */
    public function notifications($page, NotificationRepository $notifyRepo, UserRepository $userRepo, Paginator $paginator)
    {
        $user = $userRepo->findOneBy(['username' => $this->getUser()->getUsername()]);

        if ($notifyRepo->count(['receiver' => $user, 'seen' => false])) {
            foreach ($notifyRepo->findBy(['receiver' => $user,'seen' => false]) as $notification) {
                $notification->setSeen(true);
            }
            $this->getDoctrine()->getManager()->flush();
        }

        $paginator
            ->setClass(Notification::class)
            ->setCriteria($user)
            ->setOrder(['createdAt' => 'DESC'])
            ->setMethod('findUserNotifications')
            ->setLimit(10)
            ->setPage($page);

        return $this->render('user/notifications.html.twig', [
            'notifications' => $paginator->getData(),
            'paginator' => $paginator
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
            ->setOrder(['addedAt' => 'DESC'])
            ->setCriteria(['user' => $user,'published' => true])
            ->setMethod('findUserBookmarks')
            ->setLimit(10)
            ->setPage($page)
        ;

        return $this->render('user/bookmarks.html.twig', [
            'posts' => $paginator->getData(),
            'paginator' => $paginator
        ]);
    }

    /**
     * @Route ("/settings", name="user_settings")
     * @param Request $request
     * @param UserRepository $repo
     * @return Response
     */
    public function settings(Request $request, UserRepository $repo): Response
    {
        $user = $repo->findOneBy(['username'=>$this->getUser()->getUsername()]);
        $form = $this->createForm(ProfileType::class,$user->getProfile());
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

    /**
     * @Route("/edit", name="edit")
     * @param Request $request
     * @param UserRepository $repo
     * @param UploadHandler $handler
     * @return Response
     */
    public function edit(Request $request, UserRepository $repo, UploadHandler $handler)
    {
        $user = $repo->findOneBy(['username' => $this->getUser()->getUsername()]);
        $form = $this->createForm(ProfileType::class, $user->getProfile());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('avatarDelete')->getData() == true) {
                $handler->remove($user->getProfile(),'avatarFile');
                $user->getProfile()->setAvatar('avatar.jpg');
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', $this->trans('profile.changes.saved'));
            return $this->redirectToRoute('user_profile', ['username' => $user->getUsername()]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
}
