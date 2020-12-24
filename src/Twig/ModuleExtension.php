<?php

namespace App\Twig;

use App\Repository\BookmarkRepository;
use App\Repository\CommentRepository;
use App\Repository\NotificationRepository;
use App\Repository\PostRepository;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ModuleExtension extends AbstractExtension
{
    private $posts;
    private $bookmarks;
    private $notifyRepo;
    private $comments;

    public function __construct(PostRepository $postRepo, BookmarkRepository $bookmarks, NotificationRepository $notifyRepo, CommentRepository $commentRepo)
    {
        $this->posts = $postRepo;
        $this->comments = $commentRepo;
        $this->bookmarks = $bookmarks;
        $this->notifyRepo = $notifyRepo;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('userMenu', [$this, 'userMenu'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new TwigFunction('dashUserMenu', [$this, 'dashUserMenu'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new TwigFunction('userContainPost', [$this, 'userContainPost'], ['is_safe' => ['html']]),
            new TwigFunction('moderationCount', [$this, 'moderationCount'], ['is_safe' => ['html']]),
            new TwigFunction('notifyCount', [$this, 'notifyCount'], ['is_safe' => ['html']]),
            new TwigFunction('notifyCountBadge', [$this, 'notifyCountBadge'], ['is_safe' => ['html']]),
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
            'post_moderation' => $this->posts->count(['published' => null]),
            'comment_moderation' => count($this->comments->complaintsMoreThan(10))
        ]);
    }

    public function userContainPost($user, $post)
    {
        return $this->bookmarks->findOneBy(['user' => $user, 'post' => $post]);
    }

    public function moderationCount()
    {
        $postsCount = $this->posts->count(['published' => null]);
        $commentsCount = count($this->comments->complaintsMoreThan(10));
        return $postsCount + $commentsCount;
    }

    public function notifyCount($user)
    {
        return $this->notifyRepo->count(['receiver' => $user, 'seen' => false]);
    }

    public function notifyCountBadge($user)
    {
        $template = '<span class="badge badge-pill badge-danger">%s</span>';
        $count = $this->notifyRepo->count(['receiver' => $user, 'seen' => false]);

        if ($count >= 1) {
            $result = sprintf($template, $count);
        } else {
            $result = null;
        }

        return $result;
    }
}
