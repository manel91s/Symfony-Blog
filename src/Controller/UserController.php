<?php

namespace App\Controller;

use App\Http\DTO\ActivateRequest;
use App\Http\DTO\ChangePasswordRequest;
use App\Http\DTO\ForgotPasswordRequest;
use App\Http\DTO\LoginRequest;
use App\Http\DTO\ProfileRequest;
use App\Http\DTO\RegisterRequest;
use App\Http\DTO\RestorePasswordRequest;
use App\Repository\UserRepository;
use App\Services\LoginService;
use App\Services\RegisterService;
use App\Services\UserService;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

ini_set('memory_limit', '256M');

class UserController extends AbstractController
{
    #[Route('/user/registration', name: 'app_user_create', methods: 'POST')]
    public function registration(
        RegisterRequest $registerRequest,
        RegisterService $registerService,
        MailerInterface $mailer
    ): JsonResponse {
        try {

            $user = $registerService->registerUser($registerRequest);
            $registerService->sendEmail($mailer, $user);

            return $this->json([
                'token' => $user->getToken(),
                'msg' => 'La cuenta de usuario se ha creado correctamente, revisa tu email 
                para confirmar tu cuenta.'
            ], 201);
        } catch (BadRequestException $e) {
            return $this->json(['msg' => $e->getMessage()], $e->getCode());
        }
    }

    #[Route('/user/update/profile', name: 'app_user_profile', methods: 'POST')]
    public function updateProfile(
        ProfileRequest $profileRequest,
        UserService $userService,
        JWTEncoderInterface $jwtEncoder,
        KernelInterface $kernel
    ): JsonResponse {
        try {

            $userService->setEncoder($jwtEncoder);
            $userService->setFileUploader($kernel);

            $user = $userService->updateProfile($profileRequest);

            return $this->json([
                'id' => $user->getId(),
                'name' => $user->getName(),
                'surname' => $user->getSurname(),
                'email' => $user->getEmail(),
                'avatar' => $user->getAvatar()
            ], 200);
        } catch (BadRequestException $e) {
            return $this->json(['msg' => $e->getMessage()], $e->getCode());
        }
    }

    #[Route('/user/update/password', name: 'app_user_password', methods: 'PATCH')]
    public function updatePassword(
        ChangePasswordRequest $changePasswordRequest,
        UserService $userService,
        JWTEncoderInterface $jwtEncoder,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        try {

            $userService->setEncoder($jwtEncoder);
            $userService->changePassword($changePasswordRequest, $passwordHasher);

            return $this->json(['msg' => 'La contraseña se ha actualizado correctamente'], 200);
        } catch (BadRequestException $e) {
            return $this->json(['msg' => $e->getMessage()], $e->getCode());
        }
    }

    #[Route('/user/login', name: 'app_user_login', methods: 'POST')]
    public function login(
        LoginRequest $loginRequest,
        UserRepository $UserRepository,
        JWTEncoderInterface $jwtEncoder,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        try {

            $loginService = new LoginService($UserRepository, $jwtEncoder, $passwordHasher);

            $user = $loginService->login($loginRequest);

            return $this->json([
                'id' => $user->getId(),
                'name' => $user->getName(),
                'surname' => $user->getSurname(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles()[0],
            ], 200);
        } catch (BadRequestException $e) {

            return $this->json([
                'msg' => $e->getMessage(),
            ], $e->getCode());
        }
    }
    #[Route('/user/activate/{token}', name: 'app_user_activate', methods: 'GET')]
    public function activation(ActivateRequest $activateRequest, LoginService $loginService)
    {
        try {

            $loginService->activeUser($activateRequest);

            return $this->json([
                'msg' => 'La cuenta de usuario se ha activado correctamente',
            ], 200);
        } catch (BadRequestException $e) {
            return $this->json([
                'msg' => $e->getMessage(),
            ], $e->getCode());
        }
    }

    #[Route('/user/forgot-password', name: 'app_user_forgot_password', methods: 'PATCH')]
    public function forgotPassword(
        ForgotPasswordRequest $forgotPasswordRequest,
        LoginService $loginService,
        MailerInterface $mailer
    ) {
        try {

            $loginService->forgotPassword($forgotPasswordRequest, $mailer);

            return $this->json([
                'msg' => 'Se ha enviado un email para restablecer la contraseña',
            ], 200);
        } catch (BadRequestException $e) {
            return $this->json([
                'msg' => $e->getMessage(),
            ], $e->getCode());
        }
    }

    #[Route('/user/restore-password/{token}', name: 'app_user_restore_password', methods: 'PATCH')]
    public function restorePassword(
        RestorePasswordRequest $changePasswordRequest,
        UserService $userService,
        JWTEncoderInterface $jwtEncoder,
        UserPasswordHasherInterface $passwordHasher
    ) {
        try {

            $userService->setEncoder($jwtEncoder);
            $userService->restorePassword($changePasswordRequest, $passwordHasher);

            return $this->json(['msg' => 'La contraseña se ha actualizado correctamente'], 200);
        } catch (BadRequestException $e) {
            return $this->json(['msg' => $e->getMessage()], $e->getCode());
        }
    }
}
