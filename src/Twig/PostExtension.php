<?php

namespace App\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PostExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('postInfo', [$this, 'postInfo'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new TwigFunction('postTitle', [$this, 'postTitle'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new TwigFunction('postCategories', [$this, 'postCategories'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new TwigFunction('postImage', [$this, 'postImage'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new TwigFunction('postDescription', [$this, 'postDescription'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new TwigFunction('postActions', [$this, 'postActions'], ['is_safe' => ['html'], 'needs_environment' => true]),
        ];
    }

    public function postInfo(Environment $twig, $post)
    {
        return $twig->render('layouts/post/post_info.html.twig', [
            'post' => $post
        ]);
    }

    public function postTitle(Environment $twig, $post, $link = true)
    {
        return $twig->render('layouts/post/post_title.html.twig', [
            'post' => $post,
            'link' => $link
        ]);
    }

    public function postCategories(Environment $twig, $post)
    {
        return $twig->render('layouts/post/post_categories.html.twig', [
            'post' => $post
        ]);
    }

    public function postImage(Environment $twig, $post)
    {
        return $twig->render('layouts/post/post_image.html.twig', [
            'post' => $post
        ]);
    }

    public function postDescription(Environment $twig, $post, $text = null)
    {
        return $twig->render('layouts/post/post_description.html.twig', [
            'post' => $post,
            'text' => $text
        ]);
    }

    public function postActions(Environment $twig, $post, $comments = true)
    {
        return $twig->render('layouts/post/post_actions.html.twig', [
            'post' => $post,
            'comments' => $comments
        ]);
    }
}
