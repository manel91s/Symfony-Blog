<?php

namespace App\Services;

use App\Entity\Mailer;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService {
    
    private $userRepository;

    public function __construct(UserRepository $userRepository) 
    {
        $this->userRepository = $userRepository;
    }

    public function hassPassword(User $user, UserPasswordHasherInterface $passwordHasher) : String
    {
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $user->getPassword()
        );

        return $hashedPassword;
    }

    public function generateToken() : string
    {
        return sha1(random_bytes(12));
    }

    public function sendEmail($user) 
    {
        $mailer = new Mailer();
        
        $mailer->sendEmail($user);
    }
}