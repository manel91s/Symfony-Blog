<?php

namespace App\Services;

use App\Entity\User;
use App\Http\DTO\RegisterRequest;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Container\ContainerInterface;
use Faker\Core\File;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterService
{

    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private UserService $userService;
    private EntityManagerInterface $entityManager;
    private $projectDir;


    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, KernelInterface $kernel)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->userService = new UserService($this->userRepository);
        $this->projectDir = $kernel->getProjectDir();
    }

    /**
     * Registering the user
     * @return User
     */
    public function registerUser(RegisterRequest $request): User
    {

        $this->entityManager->beginTransaction();

        try {

            if ($this->isUserRegistered($request)) {
                throw new BadRequestException("Este email ya está registrado", Response::HTTP_CONFLICT);
            }
 
            $user = new User(
                $request->getName(),
                $request->getSurname(),
                $request->getEmail(),
                $request->getPassword(),
                ['USER']
            );
            $user->setPassword($this->hashPassword($user));
            $user->setToken($this->generateToken());

            if ($request->getFile()) {
                $fileName = $this->uploadAvatar($request->getFile());
                $user->setAvatar($fileName);
            }

            $this->userRepository->save($user, true);

            $this->entityManager->commit();
        } catch (BadRequestException $e) {

            $this->entityManager->rollback();

            throw new BadRequestException($e->getMessage(), $e->getCode());
        }

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
            throw new BadRequestException($e->getMessage(), Response::HTTP_BAD_REQUEST);
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

    /**
     * Upload the avatar
     * @return string
     */
    public function uploadAvatar(UploadedFile $file): string
    {

        $fileUploader = new FileUploader($this->projectDir . '/public/uploads/avatar');

        try {

            if ($file->getSize() > 1000000) {
                throw new BadRequestException("El archivo es demasiado grande", Response::HTTP_BAD_REQUEST);
            }

            if ($file->getMimeType() !== 'image/jpeg' && $file->getMimeType() !== 'image/png') {
                throw new BadRequestException("El archivo no es una imagen", Response::HTTP_BAD_REQUEST);
            }
        } catch (BadRequestException $e) {
            throw new BadRequestException($e->getMessage(), $e->getCode());
        }

        return $fileUploader->upload($file);
    }
}
