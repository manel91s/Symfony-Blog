<?php

namespace App\Services;

use App\Entity\User;
use App\Http\DTO\ChangePasswordRequest;
use App\Http\DTO\CheckProfileRequest;
use App\Http\DTO\ProfileRequest;
use App\Http\DTO\RestorePasswordRequest;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\UserNotFoundException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    private UserRepository $userRepository;
    private jwtService $jwtService;
    private FileUploader $fileUploader;


    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Check profile User
     */
    public function checkProfile(CheckProfileRequest $request)
    {
        $bearerToken = $this->jwtService->getTokenFromRequest($request);
        $payload = $this->jwtService->decodeToken($bearerToken);

        if (!$user = $this->checkUserById($payload['userId'])) {
            throw new BadRequestException("Este usuario no existe", Response::HTTP_CONFLICT);
        }

        return $user;
    }

    /**
     * get Users
     */
    public function getUsers(CheckProfileRequest $request)
    {
        $bearerToken = $this->jwtService->getTokenFromRequest($request);
        $payload = $this->jwtService->decodeToken($bearerToken);

        if (!$user = $this->checkUserById($payload['userId'])) {
            throw new BadRequestException("Este usuario no existe", Response::HTTP_CONFLICT);
        }

        if($user->getRoles()[0] !== 'ADMIN') {
            throw new BadRequestException(
                "No tienes permisos para recuperar esta información", Response::HTTP_CONFLICT
            );
        }

        $users = $this->userRepository->findAll();

        $arrayUsers = [];
        foreach($users as $user) {
            $arrayUsers[] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'avatar' => $user->getAvatar()
            ];
        }
        return $arrayUsers;
    }

    /**
     * Update password User
     */
    public function changePassword(
        ChangePasswordRequest $request,
        UserPasswordHasherInterface $userPasswordHasher
    ) {
        $bearerToken = $this->jwtService->getTokenFromRequest($request);
        $payload = $this->jwtService->decodeToken($bearerToken);

        if (!$user = $this->checkUserById($payload['userId'])) {
            throw new BadRequestException("Este usuario no existe", Response::HTTP_CONFLICT);
        }

        if (!$userPasswordHasher->isPasswordValid($user, $request->getOldPassword())) {
            throw new BadRequestException("Las credenciales proporcionadas no son validas", Response::HTTP_UNAUTHORIZED);
        }

        $hashedPassword = $userPasswordHasher->hashPassword($user, $request->getNewPassword());
        $user->setPassword($hashedPassword);

        $this->userRepository->save($user, true);
    }

    /**
     * Update the user
     * @return User
     */
    public function updateProfile(ProfileRequest $request): User
    {
        try {

            $bearerToken = $this->jwtService->getTokenFromRequest($request);
            $payload = $this->jwtService->decodeToken($bearerToken);

            if (!$user = $this->checkUserById($payload['userId'])) {
                throw new BadRequestException("Este usuario no existe", Response::HTTP_CONFLICT);
            }

            $user->setName($request->getName());
            $user->setSurname($request->getSurname());
            $user->setEmail($request->getEmail());

            if ($user->getAvatar() && $request->getFile()) {
                $this->fileUploader->remove($user->getAvatar());
                $fileName = $this->uploadImage($request->getFile());
                $user->setAvatar($fileName);
            }

            $this->userRepository->save($user, true);
        } catch (BadRequestException $e) {
            throw new BadRequestException($e->getMessage(), $e->getCode());
        }

        return $user;
    }

    /**
     * Restore password User
     */
    public function restorePassword(
        RestorePasswordRequest $request,
        UserPasswordHasherInterface $userPasswordHasher
    ) {
        if (!$user = $this->checkUserByToken($request->getToken())) {
            throw new BadRequestException("El token solicitado no existe", Response::HTTP_CONFLICT);
        }

        $hashedPassword = $userPasswordHasher->hashPassword($user, $request->getPassword());
        $user->setPassword($hashedPassword);
        $user->setToken(null);

        $this->userRepository->save($user, true);
    }

    public function checkUser(string $email): ?User
    {
        return $this->userRepository->findOneByEmail($email);
    }

    public function checkUserById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function checkUserByToken(string $token): ?User
    {
        return $this->userRepository->findOneBy(['token' => $token]);
    }

    public function setEncoder(JWTEncoderInterface $jwtEncoder): void
    {
        $this->jwtService = new jwtService($jwtEncoder);
    }

    public function setFileUploader(KernelInterface $kernel): void
    {
        $this->fileUploader = new FileUploader($kernel->getProjectDir() . '/public/uploads/avatar');
    }

    private function uploadImage(UploadedFile $file)
    {
        try {

            if ($file->getSize() > 1000000) {
                throw new BadRequestException("El tamaño de la imagen no puede ser mayor a 1MB", Response::HTTP_BAD_REQUEST);
            }

            if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png'])) {
                throw new BadRequestException("El formato de la imagen no es válido", Response::HTTP_BAD_REQUEST);
            }
        } catch (BadRequestException $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $this->fileUploader->upload($file);
    }
}
