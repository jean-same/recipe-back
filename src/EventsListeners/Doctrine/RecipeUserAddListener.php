<?php

namespace App\EventsListeners\Doctrine;

use App\Entity\Recipe;
use Symfony\Component\Security\Core\Security;

class RecipeUserAddListener
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function prePersist(Recipe $recipe)
    {
        $user = $this->security->getUser();
        $recipe->setUser($user);
    }

}