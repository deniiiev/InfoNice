<?php

namespace App\Controller\Dashboard;

use App\Entity\Comment;
use App\Entity\Post;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Service\Paginator;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dash", name="dash_")
 */
class DashModerationController extends AbstractController
{
    /**
     * @Route("/moderation/posts/{page<\d+>?1}", name="moderation_posts")
     * @param PostRepository $repo
     * @return Response
     */
    public function moderation(PostRepository $repo): Response
    {
        $posts = $repo->findBy(['published' => null], ['createdAt' => 'ASC']);
        return $this->render('dashboard/home/moderation.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/moderation/comments/{page<\d+>?1}", name="moderation_comments")
     * @param $page
     * @param Paginator $paginator
     * @return Response
     */
    public function comments($page,Paginator $paginator): Response
    {
        $paginator
            ->setClass(Comment::class)
            ->setOrder(['publishedAt' => 'DESC'])
            ->setMethod('complaintsMoreThan')
            ->setCriteria(10)
            ->setLimit(10)
            ->setPage($page)
        ;

        return $this->render('dashboard/home/moderation_comments.html.twig', [
            'comments' => $paginator->getData(),
            'paginator' => $paginator
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
        return $this->redirectToRoute('dash_moderation_posts');
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

    /**
     * @Route("/moderation/cancel/{id}", name="moderation_cancel")
     * @param Post $post
     * @return Response
     */
    public function cancel(Post $post):Response
    {
        $em = $this->getDoctrine()->getManager();
        $post->setPublished(false);
        $post->setPublishedAt(new DateTime('now'));
        $em->flush();
        return $this->redirectToRoute('dash_moderation_posts');
    }
}
