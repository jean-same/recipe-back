<?php

namespace App\Service;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CheckUserAuthorizationService
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    public function isAllow($recipe) {

        $user = $this->security->getUser();
        
        if($recipe->getUser() !== $user) {
            throw new AccessDeniedException("Accès interdit");
        }
        
        return false ;
    }
}