<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService {
    
    private $repository;

    public function __construct(EntityManagerInterface $repository) 
    {
        $this->repository = $repository;
    }

    public function register(User $user, UserPasswordHasherInterface $passwordHasher) : User
    {
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $user->getPassword()
        );
        $user->setPassword($hashedPassword);
      
        $this->repository->persist($user);
        $this->repository->flush();
        
        return $user;
    }

}