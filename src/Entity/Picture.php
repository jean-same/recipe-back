<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PictureRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PictureRepository::class)]
class Picture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['app_v1_recipe_browse' , 'app_v1_type_browse', 'app_v1_user_browse'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['app_v1_recipe_browse' , 'app_v1_type_browse', 'app_v1_user_browse'])]
    private $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['app_v1_recipe_browse' , 'app_v1_type_browse', 'app_v1_user_browse'])]
    private $url;

    #[ORM\ManyToOne(targetEntity: Recipe::class , inversedBy: 'pictures')]
    #[ORM\JoinColumn(nullable: false)]
    private $recipe;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

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
