<?php

namespace App\Controller;

use App\Entity\Bookmark;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Notification;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\Post\AdType;
use App\Form\Post\NewsType;
use App\Form\Post\QuestionType;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Service\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/section/{section}/{page<\d+>?1}", name="post_section")
     * @param $section
     * @param $page
     * @param CategoryRepository $categoryRepo
     * @param Paginator $paginator
     * @return string
     */
    public function section($section,$page, CategoryRepository $categoryRepo, Paginator $paginator)
    {
        $categories = $categoryRepo->findBy([$section => true]);

        $paginator
            ->setClass(Post::class)
            ->setOrder(['publishedAt' => 'DESC'])
            ->setCriteria(['section'=>$section,'published' => true])
            ->setParameters(['section'=>$section])
            ->setMethod('findPostsBy')
            ->setLimit(10)
            ->setPage($page)
        ;

        return $this->render('post/section.html.twig', [
            'posts' => $paginator->getData(),
            'paginator' => $paginator,
            'categories' => $categories,
            'section' => $section
        ]);
    }

    /**
     * @Route("/section/{section}/{slug}/{page<\d+>?1}", name="post_category")
     * @param $section
     * @param Category $category
     * @param $page
     * @param Paginator $paginator
     * @param CategoryRepository $categoryRepo
     * @return string
     */
    public function category($section, Category $category, $page, Paginator $paginator, CategoryRepository $categoryRepo)
    {
        $categories = $categoryRepo->findBy([$section => true]);

        $paginator
            ->setClass(Post::class)
            ->setOrder(['publishedAt' => 'DESC'])
            ->setCriteria(['section'=>$section,'published' => true,'category' => $category])
            ->setMethod('findPostsBy')
            ->setParameters(['section'=>$section,'slug'=>$category->getSlug()])
            ->setLimit(10)
            ->setPage($page)
        ;

        $parameters = [
            'section' => 'news',
            'slug' => 'dokumenty'
        ];

        return $this->render('post/category.html.twig',[
            'posts' => $paginator->getData(),
            'paginator' => $paginator,
            'categories' => $categories,
            'section' => $section,
            'category' => $category,
            'parameters' => $parameters
        ]);
    }

    /**
     * @Route("/post/{id}/{page<\d+>?1}", name="post_show")
     * @param Post $post
     * @param $page
     * @param Paginator $paginator
     * @param Request $request
     * @param UserRepository $userRepo
     * @return Response
     */
    public function show(Post $post, $page, Paginator $paginator, Request $request, UserRepository $userRepo): Response
    {
        if ($post->getPublished() != true && $this->getUser() != $post->getAuthor() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException();
        }

        $paginator
            ->setClass(Comment::class)
            ->setOrder(['publishedAt' => 'DESC'])
            ->setCriteria(['post' => $post])
            ->setLimit(10)
            ->setParameters(['id'=>$post->getId()])
            ->setPage($page)
        ;

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = new Comment();
            $comment->setAuthor($userRepo->findOneBy(['username' => $this->getUser()->getUsername()]));
            $comment->setMessage($form->get('message')->getData());
            $comment->setAnonymous($form->get('anonymous')->getData());
            $comment->setPost($post);

            if ($form->get('replyTo')->getData()) {
                $receiver = $userRepo->findOneBy(['username' => $form->get('replyTo')->getData()]);
                $notification = new Notification();
                $notification->setReceiver($receiver);
                $notification->setComment($comment);
                $notification->setType('reply');
                $comment->setReplyTo($receiver);
                $em->persist($notification);
            }

            if ($post->getAuthor() != $this->getUser() && !$form->get('replyTo')->getData()) {
                $notification = new Notification();
                $notification->setReceiver($post->getAuthor());
                $notification->setComment($comment);
                $notification->setType('post');
                $em->persist($notification);
            }

            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('post_show',[
                'id' => $post->getId(),
                '_fragment' => 'comments'
            ]);
        }

        $post->setViews($post->getViews()+1);
        $em->persist($post);
        $em->flush();

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'comments' => $paginator->getData(),
            'pagonator' => $paginator
        ]);
    }

    /**
     * @Route("/add/{section}", name="post_add", methods={"GET","POST"})
     * @param $section
     * @param Request $request
     * @return Response
     */
    public function add($section,Request $request): Response
    {
        $sections = ['news' => NewsType::class, 'ads' => AdType::class, 'questions' => QuestionType::class];

        $post = new Post();
        $form = $this->createForm($sections[$section], $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setAuthor($this->getDoctrine()->getRepository(User::class)->findOneBy(['username'=>$this->getUser()->getUsername()]));
            $post->setSection($section);

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('post/add.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'section' => $section
        ]);
    }

    /**
     * @Route("/bookmarker/{slug}", name="post_bookmarker", methods={"POST", "GET"})
     * @param Post $post
     * @param UserRepository $users
     * @return JsonResponse
     */
    public function bookmarker(Post $post, UserRepository $users): Response
    {
        $user = $users->findOneBy(['username' => $this->getUser()->getUsername()]);
        $contains = $this->getDoctrine()->getRepository(Bookmark::class)->findOneBy(['user' => $user, 'post' => $post]);
        $em = $this->getDoctrine()->getManager();

        if ($contains) {
            $user->removeBookmark($contains);
            $response = ['status' => 'removed'];
        } else {
            $bookmark = new Bookmark();
            $bookmark->setUser($user);
            $bookmark->setPost($post);
            $em->persist($bookmark);
            $response = ['status' => 'added'];
        }

        $em->flush();

        return $this->json([
            'response' => $response
        ]);
    }

    /**
     * @Route("/featured/{id}", name="post_featured", methods={"POST", "GET"})
     * @param Post $post
     * @return JsonResponse
     */
    public function featured(Post $post): Response
    {
        if ($post->getFeatured()){
            $response = ['status' => 'removed'];
            $post->setFeatured(false);
        } else {
            $response = ['status' => 'added'];
            $post->setFeatured(true);
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->json([
            'response' => $response
        ]);
    }

    /**
     * @Route("/post/comment/complaint/{id}", name="post_comment_complaint")
     * @param Comment $comment
     * @return Response
     */
    public function complaint(Comment $comment): Response
    {
        $comment->setComplaints($comment->getComplaints()+1);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('post_show',[
            'id' => $comment->getPost()->getId()
        ]);
    }

    /**
     * @Route("/post/comment/delete/{id}", name="post_comment_delete")
     * @param Comment $comment
     * @return Response
     */
    public function delete(Comment $comment): Response
    {
        if ($this->getUser() == $comment->getAuthor() || $this->isGranted('ROLE_ADMIN')) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();
        }

        return $this->redirectToRoute('post_show',[
            'id' => $comment->getPost()->getId()
        ]);
    }
}
