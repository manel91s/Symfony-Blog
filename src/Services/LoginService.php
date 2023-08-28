<?php

namespace App\Services;

use App\Controller\Api\Listener\JWTDecodedListener;
use App\Entity\User;
use App\Http\DTO\ActivateRequest;
use App\Http\DTO\LoginRequest;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginService
{
    private UserService $userService;
    private UserRepository $userRepository;
    private jwtService $jwtService;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        UserRepository $userRepository, 
        JWTEncoderInterface $jwtEncoder, 
        UserPasswordHasherInterface $passwordHasher
        )
    {
        $this->userRepository = $userRepository;
        $this->userService = new UserService($this->userRepository);
        $this->jwtService = new jwtService($jwtEncoder);
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Logging the user
     * @return User
     */
    public function login(LoginRequest $request): User
    {
        $user = $this->userService->checkUser($request->getEmail());

        if (!$user) {
            throw new BadRequestException("Este email no está registrado", Response::HTTP_CONFLICT);
        }
        
        $bearerToken = $this->jwtService->getTokenFromRequest($request);
        $payload = $this->jwtService->decodeToken($bearerToken);

        if ($user && $user->getId() !== $payload['userId']) {
            throw new BadRequestException("Estas accediendo con un usuario incorrecto", Response::HTTP_UNAUTHORIZED);
        }

        if ($user && !$this->comparePasword($user, $request->getPassword())) {
            throw new BadRequestException("Contraseña incorrecta", Response::HTTP_UNAUTHORIZED);
        }

        if($user && !$user->isConfirm()) {
            throw new BadRequestException("La cuenta no está activada", Response::HTTP_UNAUTHORIZED);
        }

        return $user;
    }

    /**
     * Compare the password
     * @return bool
     */
    private function comparePasword(User $user, string $plainPassword): bool
    {
        return $this->passwordHasher->isPasswordValid($user, $plainPassword);
    }

    public function activeUser(ActivateRequest $request): void
    {

        try {

            $user = $this->userService->checkUserByToken($request->getToken());

            if (!$user) {
                throw new BadRequestException("El token no es válido", Response::HTTP_UNAUTHORIZED);
            }

            $user->setToken(null);
            $user->setConfirm(true);
            
            $this->userRepository->save($user, true);
            
        }catch(BadRequestException $e) {
            throw new BadRequestException("Error al activar la cuenta", Response::HTTP_UNAUTHORIZED);
        }

    }

}
