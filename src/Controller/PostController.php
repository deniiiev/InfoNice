<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/post/{id}", name="post_show")
     * @param Post $post
     * @return Response
     */
    public function show(Post $post): Response
    {
//        if ($post->getStatus() != true) {
//            throw $this->createNotFoundException();
//        }
        return $this->render('post/show.html.twig', [
            'post' => $post
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
     * @Route("/addPost", name="post_add", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setAuthor($this->getDoctrine()->getRepository(User::class)->findOneBy(['username'=>$this->getUser()->getUsername()]));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('post/add.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }
}
