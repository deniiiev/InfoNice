<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\Post\AdType;
use App\Form\Post\NewsType;
use App\Form\Post\QuestionType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/news", name="post_news")
     * @param PostRepository $postRepo
     * @return string
     */
    public function news(PostRepository $postRepo)
    {
        $posts = $postRepo->findBy(['section'=>'news','published' => true], ['publishedAt' => 'DESC']);

        return $this->render('post/news.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/ads", name="post_ads")
     * @param PostRepository $postRepo
     * @return string
     */
    public function ads(PostRepository $postRepo)
    {
        $posts = $postRepo->findBy(['section'=>'ad', 'published' => true], ['publishedAt' => 'DESC']);

        return $this->render('post/ads.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/questions", name="post_questions")
     * @param PostRepository $postRepo
     * @return string
     */
    public function questions(PostRepository $postRepo)
    {
        $posts = $postRepo->findBy(['section'=>'question', 'published' => true], ['publishedAt' => 'DESC']);

        return $this->render('post/questions.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/post/{id}", name="post_show")
     * @param Post $post
     * @param Request $request
     * @param UserRepository $repo
     * @return Response
     */
    public function show(Post $post, Request $request, UserRepository $repo): Response
    {
        if ($post->getPublished() != true && $this->getUser() != $post->getAuthor() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = new Comment();
            $comment->setAuthor($repo->findOneBy(['username' => $this->getUser()->getUsername()]));
            $comment->setMessage($form->get('message')->getData());
            $comment->setPost($post);
            $em->persist($comment);
        }

        $post->setViews($post->getViews()+1);
        $em->persist($post);
        $em->flush();

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/posts/{slug}", name="post_category")
     * @param Category $category
     * @return Response
     */
    public function category(Category $category): Response
    {
        return $this->render('post/category.html.twig', [
            'posts' => $category->getPosts(),
            'category' => $category
        ]);
    }

    /**
     * @Route("/add/{type}", name="post_add", methods={"GET","POST"})
     * @param $type
     * @param Request $request
     * @return Response
     */
    public function add($type,Request $request): Response
    {
        $types = ['news' => NewsType::class, 'ad' => AdType::class, 'question' => QuestionType::class];

        $post = new Post();
        $form = $this->createForm($types[$type], $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setAuthor($this->getDoctrine()->getRepository(User::class)->findOneBy(['username'=>$this->getUser()->getUsername()]));
            $post->setSection($type);

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('post/add.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'type' => $type
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
}
