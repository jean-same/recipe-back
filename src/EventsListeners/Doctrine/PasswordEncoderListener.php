<?php

namespace App\EventsListener\Doctrine;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordEncoderListener
{

    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function prePersist( User $user)
    {
        $hash = $this->passwordHasher->hashPassword($user, $user->getPassword() );
        $user->setPassword($hash);
    }

    public function preUpdate(User $user)
    { 
        $hash = $this->passwordHasher->hashPassword( $user, $user->getPassword());
        $user->setPassword($hash);
    }
}