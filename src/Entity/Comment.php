<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommentRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['app_v1_recipe_browse' , 'app_v1_type_browse', 'app_v1_user_browse'])]
    private $id;

    #[ORM\Column(type: 'text')]
    #[Groups(['app_v1_recipe_browse' , 'app_v1_user_browse'])]
    private $content;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['app_v1_recipe_browse' , 'app_v1_user_browse'])]
    private $created_at;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['app_v1_recipe_browse' , 'app_v1_user_browse'])]
    private $user;

    #[ORM\ManyToOne(targetEntity: Recipe::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private $recipe;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): self
    {
        $this->recipe = $recipe;

        return $this;
    }
}
