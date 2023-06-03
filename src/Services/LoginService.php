<?php

namespace App\Services;

use App\Controller\Api\Listener\JWTDecodedListener;
use App\Entity\User;
use App\Http\DTO\LoginRequest;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginService
{
    private JWTDecodedListener $jwtDecodedListener;
    private UserService $userService;
    private UserRepository $userRepository;
    private JWTEncoderInterface $jwtEncoder;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserRepository $userRepository, JWTEncoderInterface $jwtEncoder, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userRepository = $userRepository;
        $this->userService = new UserService($this->userRepository);
        $this->jwtEncoder = $jwtEncoder;
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

        $bearerToken = $this->getTokenFromRequest($request);
        $payload = $this->decodeToken($bearerToken);

        if ($user && $user->getId() !== $payload['userId']) {
            throw new BadRequestException("Estas accediendo con un usuario incorrecto", Response::HTTP_UNAUTHORIZED);
        }

        if ($user && !$this->comparePasword($user, $request->getPassword())) {
            throw new BadRequestException("Contraseña incorrecta", Response::HTTP_UNAUTHORIZED);
        }

        return $user;
    }

    /**
     * Get the token from the request
     * @return string
     */
    private function getTokenFromRequest(LoginRequest $request): string
    {
        $authorizationHeader = $request->authorizationHeader();

        if (!$authorizationHeader) {
            throw new BadRequestException('No se encontró el encabezado de autorización', Response::HTTP_BAD_REQUEST);
        }

        if (strpos($authorizationHeader, 'Bearer ') !== 0) {
            return new BadRequestException('El encabezado de autorización no es válido', Response::HTTP_BAD_REQUEST);
        }

        return substr($authorizationHeader, 7);
    }

    /**
     * Decode the token
     * @return array|null
     */
    private function decodeToken($token): array
    {
        return $this->jwtEncoder->decode($token);
    }

    /**
     * Compare the password
     * @return bool
     */
    private function comparePasword(User $user, string $plainPassword): bool
    {
        return $this->passwordHasher->isPasswordValid($user, $plainPassword);
    }
}
