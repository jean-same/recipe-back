<?php

namespace App\EventsListeners\Doctrine;

use App\Entity\Comment;
use Symfony\Component\Security\Core\Security;

class SetCommentUserListener
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function prePersist( Comment $comment)
    {
        $user = $this->security->getUser();
        $comment->setUser($user);
    }

    public function preUpdate( Comment $comment)
    { 
       /* $user = $this->security->getUser();
        $comment->setUser($user); */
    }
}