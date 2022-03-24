<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['app_v1_recipe_browse' , 'app_v1_type_browse', 'app_v1_user_browse'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['app_v1_recipe_browse' , 'app_v1_type_browse', 'app_v1_user_browse'])]
    private $title;

    #[ORM\Column(type: 'text')]
    #[Groups(['app_v1_recipe_browse' , 'app_v1_type_browse', 'app_v1_user_browse'])]
    private $content;

    #[ORM\Column(type: 'integer')]
    #[Groups(['app_v1_recipe_browse' , 'app_v1_type_browse', 'app_v1_user_browse'])]
    private $likes;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['app_v1_recipe_browse' , 'app_v1_type_browse', 'app_v1_user_browse'])]
    private $created_at;

    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: Comment::class, orphanRemoval: true)]
    #[Groups(['app_v1_recipe_browse' , 'app_v1_type_browse', 'app_v1_user_browse'])]
    private $comments;

    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EAGER' , inversedBy: 'recipes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['app_v1_recipe_browse' , 'app_v1_type_browse'])]
    private $user;

    #[ORM\ManyToOne(targetEntity: Type::class, inversedBy: 'recipes')]
    #[Groups(['app_v1_recipe_browse', 'app_v1_user_browse'])]
    private $type;

    #[ORM\ManyToMany(targetEntity: Ingredient::class, fetch: 'EAGER', cascade:["persist"], mappedBy: 'recipe')]
    #[Groups(['app_v1_recipe_browse' , 'app_v1_type_browse', 'app_v1_user_browse'])]
    private $ingredients;

    #[ORM\ManyToOne(targetEntity: Difficulty::class, inversedBy: 'recipes')]
    #[Groups(['app_v1_recipe_browse' , 'app_v1_type_browse', 'app_v1_user_browse'])]
    private $difficulty;

    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: Picture::class, cascade:["persist"], orphanRemoval: true)]
    #[Groups(['app_v1_recipe_browse' , 'app_v1_type_browse', 'app_v1_user_browse'])]
    private $pictures;

    #[ORM\Column(type: 'boolean')]
    private $to_validate;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->ingredient = new ArrayCollection();
        $this->ingredients = new ArrayCollection();
        $this->pictures = new ArrayCollection();
        $this->created_at = new DateTimeImmutable();
        $this->likes = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setRecipe($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getRecipe() === $this) {
                $comment->setRecipe(null);
            }
        }

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

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Ingredient>
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(Ingredient $ingredient): self
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients[] = $ingredient;
            $ingredient->addRecipe($this);
        }

        return $this;
    }

    public function removeIngredient(Ingredient $ingredient): self
    {
        if ($this->ingredients->removeElement($ingredient)) {
            $ingredient->removeRecipe($this);
        }

        return $this;
    }

    public function getDifficulty(): ?Difficulty
    {
        return $this->difficulty;
    }

    public function setDifficulty(?Difficulty $difficulty): self
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    /**
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setRecipe($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getRecipe() === $this) {
                $picture->setRecipe(null);
            }
        }

        return $this;
    }

    public function getToValidate(): ?bool
    {
        return $this->to_validate;
    }

    public function setToValidate(bool $to_validate): self
    {
        $this->to_validate = $to_validate;

        return $this;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): self
    {
        $this->likes = $likes;

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

}
