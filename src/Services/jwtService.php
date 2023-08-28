<?php

namespace App\Services;

use App\Http\DTO\RequestWithAuthorizationDTO;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;

class jwtService {

    private JWTEncoderInterface $jwtEncoder;

    public function __construct(JWTEncoderInterface $jwtEncoder)
    {
        $this->jwtEncoder = $jwtEncoder;
    }

    /**
     * Get the token from the request
     * @return string
     */
    public function getTokenFromRequest(RequestWithAuthorizationDTO $request): string
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
    public function decodeToken($token): array
    {
        return $this->jwtEncoder->decode($token);
    }

}