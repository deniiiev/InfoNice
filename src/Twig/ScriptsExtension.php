<?php

namespace App\Twig;

use App\Entity\User;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ScriptsExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('chosen', [$this, 'chosen'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new TwigFunction('ckeditor', [$this, 'ckeditor'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new TwigFunction('postStatus', [$this, 'postStatus'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new TwigFunction('crudActions', [$this, 'crudActions'], ['is_safe' => ['html'], 'needs_environment' => true])
        ];
    }

    public function chosen(Environment $twig, $limit = 3, $selector = 'select')
    {
        return $twig->render('layouts/plugins/chosen.html.twig', [
            'limit' => $limit,
            'selector' => $selector
        ]);
    }

    public function ckeditor(Environment $twig)
    {
        return $twig->render('layouts/plugins/ckeditor.html.twig');
    }

    public function postStatus(Environment $twig, $status, $text = true)
    {
        if ($text) {
            $template = '<span class="badge badge-%s">%s</span>';
        } else {
            $template = '<span class="badge badge-%s"> </span>';
        }

        if ($status === null) {
            $info = ['color' => 'warning', 'message' => 'на модерации'];
        } elseif ($status === false) {
            $info = ['color' => 'danger', 'message' => 'отклонен'];
        } else {
            $info = ['color' => 'success', 'message' => 'опубликован'];
        }

        return sprintf(
            $template,
            $info['color'],
            $info['message']
        );
    }

    public function crudActions(Environment $twig, $entity, $name)
    {
        ($entity instanceof User) ? $instance = true : $instance = false;

        return $twig->render('dashboard/layouts/actions.html.twig', [
            'entity' => $entity,
            'name' => $name,
            'user' => $instance
        ]);
    }
}
