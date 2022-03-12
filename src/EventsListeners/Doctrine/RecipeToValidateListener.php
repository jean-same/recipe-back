<?php

namespace App\EventsListeners\Doctrine;

use App\Entity\Recipe;
use Symfony\Component\Security\Core\Security;

class RecipeToValidateListener
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function prePersist(Recipe $recipe)
    {
        $user = $this->security->getUser();

        if(in_array('ROLE_CONTRIBUTOR', $user->getRoles())) {
            $recipe->setToValidate(true);
        } else {
            $recipe->setToValidate(false);
        }
    }

}