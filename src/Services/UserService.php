<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\UserNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserService 
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

    }
    public function checkUser(string $email): ?User
    {

        return $this->userRepository->findOneByEmail($email);
    }

    }