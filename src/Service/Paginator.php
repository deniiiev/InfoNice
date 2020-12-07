<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class Paginator
{
    private $manager;
    private $twig;
    private $request;
    private $class;
    private $page = 1;
    private $limit = 10;
    private $criteria = [];
    private $parameters = [null=>null];
    private $order = ['id' => 'ASC'];
    private $type = 'crud';

    public function __construct(EntityManagerInterface $manager, Environment $twig, RequestStack $request)
    {
        $this->manager  = $manager;
        $this->twig     = $twig;
        $this->request  = $request;
    }

    public function display()
    {
        $this->twig->display('layouts/pagination.html.twig', [
            'page'  => $this->page,
            'pages' => $this->getPages(),
            'route' => $this->request->getCurrentRequest()->attributes->get('_route'),
            'sort' => $this->order,
            'parameters' => $this->parameters,
            'start' => $this->getStart(),
            'end' => $this->getEnd(),
            'type' => $this->type
        ]);
    }

    public function getStart()
    {
        if ($this->page > 2) {
            $start = $this->page - 2;
        } elseif ($this->page == 2) {
            $start = $this->page - 1;
        } else {
            $start = $this->page;
        }

        return $start;
    }

    public function getEnd()
    {
        if ($this->page < $this->getPages() - 1) {
            $end = $this->page + 2;
        } elseif ($this->page == $this->getPages() - 1) {
            $end = $this->page + 1;
        } else {
            $end = $this->page;
        }

        return $end;
    }

    public function getData()
    {
        $offset = $this->page * $this->limit - $this->limit;
        $repo = $this->manager->getRepository($this->class);

        if ($this->type == 'post') {
            $data = $repo->findPostsBy($this->criteria,$this->order,$this->limit, $offset);
        } else {
            $data = $repo->findBy($this->criteria,$this->order,$this->limit, $offset);
        }

        return $data;
    }

    public function getPages()
    {
        $repo = $this->manager->getRepository($this->class);

        if ($this->type == 'post') {
            $total = count($repo->findPostsBy($this->criteria));
        } else {
            $total = count($repo->findBy($this->criteria));
        }

        return ceil($total / $this->limit);
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;
        return $this;
    }

    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }
}
