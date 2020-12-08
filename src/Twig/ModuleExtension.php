<?php

namespace App\Twig;

use App\Repository\BookmarkRepository;
use App\Repository\PostRepository;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ModuleExtension extends AbstractExtension
{
    private $posts;
    private $bookmarks;

    public function __construct(PostRepository $postRepo, BookmarkRepository $bookmarks)
    {
        $this->posts = $postRepo;
        $this->bookmarks = $bookmarks;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('userMenu', [$this, 'userMenu'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new TwigFunction('dashUserMenu', [$this, 'dashUserMenu'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new TwigFunction('userContainPost', [$this, 'userContainPost'], ['is_safe' => ['html']])
        ];
    }

    public function userMenu(Environment $twig, $sidebar = false)
    {
        return $twig->render('layouts/modules/usermenu.html.twig',[
            'sidebar' => $sidebar
        ]);
    }

    public function dashUserMenu(Environment $twig, $sidebar = false)
    {
        return $twig->render('layouts/modules/dashusermenu.html.twig',[
            'sidebar' => $sidebar,
            'in_moderation' => $this->posts->count(['published' => null])
        ]);
    }

    public function userContainPost($user, $post)
    {
        return $this->bookmarks->findOneBy(['user' => $user, 'post' => $post]);
    }
}
