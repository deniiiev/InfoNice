<?php

namespace App\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ModuleExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('userMenu', [$this, 'userMenu'], ['is_safe' => ['html'], 'needs_environment' => true])
        ];
    }

    public function userMenu(Environment $twig, $sidebar = false)
    {
        return $twig->render('layouts/modules/usermenu.html.twig',[
            'sidebar' => $sidebar
        ]);
    }
}
