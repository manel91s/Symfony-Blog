<?php

namespace App\Services;

use App\Entity\Mailer;
use App\Entity\User;
use App\Http\DTO\RegisterRequest;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterService {
    
    private $userRepository;
    private $passwordHasher;

    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher) 
    {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function registerUser(RegisterRequest $request) : User
    {
        $user = new User(
            $request->getName(),
            $request->getSurname(),
            $request->getEmail(),
            $request->getPassword(),
            ['USER']
        );
        $user->setPassword($this->hashPassword($user));
        $user->setToken($this->generateToken());

        $this->userRepository->save($user, true);

        return $user;
    }

    public function isUserRegistered(RegisterRequest $request) : ?User
    {
        return $this->userRepository->findOneBy([
            'email' => $request->getEmail()
        ]);
    }

    public function hashPassword(User $user) : string
    {
        $hashedPassword = $this->passwordHasher->hashPassword(
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