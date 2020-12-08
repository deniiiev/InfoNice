<?php

namespace App\Controller\Dashboard;

use App\Entity\Post;
use App\Form\Post\AdType;
use App\Form\Post\NewsType;
use App\Form\Post\QuestionType;
use App\Service\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dash/post", name="dash_")
 */
class DashPostController extends AbstractController
{
    /**
     * @Route("/{page<\d+>?1}", name="post_index", methods={"GET"})
     * @param $page
     * @param Paginator $paginator
     * @return Response
     */
    public function index($page, Paginator $paginator): Response
    {
        $paginator
            ->setClass(Post::class)
            ->setOrder(['createdAt' => 'DESC'])
            ->setLimit(10)
            ->setPage($page)
        ;

        return $this->render('dashboard/post/index.html.twig', [
            'posts' => $paginator->getData(),
            'paginator' => $paginator
        ]);
    }

    /**
     * @Route("/{id}/edit", name="post_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Post $post
     * @return Response
     */
    public function edit(Request $request, Post $post): Response
    {
        $sections = ['news' => NewsType::class, 'ads' => AdType::class, 'questions' => QuestionType::class];

        $form = $this->createForm($sections[$post->getSection()], $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('dash_post_index');
        }

        return $this->render('dashboard/post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="post_delete", methods={"DELETE"})
     * @param Request $request
     * @param Post $post
     * @return Response
     */
    public function delete(Request $request, Post $post): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('dash_post_index');
    }
}
