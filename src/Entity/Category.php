<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\ManyToMany(targetEntity=Post::class, mappedBy="categories")
     */
    private $posts;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $news;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ads;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $questions;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function initializePrePersistUpdate()
    {
        $slugifier = new Slugify();
        $this->slug = $slugifier->slugify($this->getTitle());
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->addCategory($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            $post->removeCategory($this);
        }

        return $this;
    }

    public function getNews(): ?bool
    {
        return $this->news;
    }

    public function setNews(?bool $news): self
    {
        $this->news = $news;

        return $this;
    }

    public function getAds(): ?bool
    {
        return $this->ads;
    }

    public function setAds(?bool $ads): self
    {
        $this->ads = $ads;

        return $this;
    }

    public function getQuestions(): ?bool
    {
        return $this->questions;
    }

    public function setQuestions(?bool $questions): self
    {
        $this->questions = $questions;

        return $this;
    }
}
