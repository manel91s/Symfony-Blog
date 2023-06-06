<?php

namespace App\Services;

use App\Entity\User;
use App\Http\DTO\RegisterRequest;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterService
{

    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;

    private $userService;

    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->userService = new UserService($this->userRepository);
    }

    /**
     * Registering the user
     * @return User
     */
    public function registerUser(RegisterRequest $request): User
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

    /**
     * Check if the user is already registered
     * @return User|null
     */
    public function isUserRegistered(RegisterRequest $request): ?User
    {
        return $this->userService->checkUser($request->getEmail());
    }

    /**
     * Send the email to the user
     * @return void
     */
    public function sendEmail(MailerInterface $mailer, User $user): void
    {
        $mailer = new MailerService($mailer);

        try {
            $mailer->sendEmail($user);
        } catch (BadRequestException $e) {
            throw new BadRequestException("Error al enviar el email", Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Hashing the password
     * @return string
     */
    private function hashPassword(User $user): string
    {
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $user->getPassword()
        );

        return $hashedPassword;
    }

    /**
     * Generate a token
     * @return string
     */
    private function generateToken(): string
    {
        return sha1(random_bytes(12));
    }
}
