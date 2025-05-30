<?php

namespace App\Entity;

use App\Repository\ArticleLikeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleLikeRepository::class)]
class ArticleLike
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'articleLikes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $article = null;

    #[ORM\ManyToOne(inversedBy: 'articleLikes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $article_user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {
        $this->article = $article;

        return $this;
    }

    public function getArticleUser(): ?User
    {
        return $this->article_user;
    }

    public function setArticleUser(?User $article_user): static
    {
        $this->article_user = $article_user;

        return $this;
    }
}
