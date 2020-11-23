<?php

namespace App\Controller\Dashboard;

use App\Entity\Post;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use DateTime;
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

    /**
     * @Route("/moderation", name="moderation")
     * @param PostRepository $repo
     * @return Response
     */
    public function moderation(PostRepository $repo): Response
    {
        $posts = $repo->findBy(['published' => false]);
        return $this->render('dashboard/home/moderation.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/moderation/publish/{id}", name="moderation_publish")
     * @param Post $post
     * @return Response
     */
    public function publish(Post $post):Response
    {
        $em = $this->getDoctrine()->getManager();
        $post->setPublished(true);
        $post->setPublishedAt(new DateTime('now'));
        $em->flush();
        return $this->redirectToRoute('dash_moderation');
    }

    /**
     * @Route("/moderation/featured/{id}", name="moderation_featured")
     * @param Post $post
     * @return Response
     */
    public function featured(Post $post):Response
    {
        $em = $this->getDoctrine()->getManager();
        $post->setFeatured(true);
        $em->flush();
        return $this->redirectToRoute('dash_moderation_publish', [
            'id' => $post->getId()
        ]);
    }
}
