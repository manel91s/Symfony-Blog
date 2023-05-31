<?php
namespace App\Controller;

use App\Controller\Api\Listener\JWTDecodedListener;
use App\Entity\User;

use App\Http\DTO\RegisterRequest;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Services\RegisterService;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

ini_set('memory_limit', '256M');

class UserController extends AbstractController
{
    #[Route('/user/registration', name: 'app_user_create', methods: 'POST')]
    public function registration(RegisterRequest $registerRequest,
    RegisterService $registerService
    ) : JsonResponse
    {
        try {
            
            if($registerService->isUserRegistered($registerRequest)){
                throw new BadRequestException("Este email ya está registrado", Response::HTTP_CONFLICT);
            }
            $user = $registerService->registerUser($registerRequest);

            if(!$user) {
                throw new BadRequestException("Habido un error al registrar el usuario", Response::HTTP_BAD_REQUEST);
            }
            
            return $this->json([
                'token' => $user->getToken(),
                'msg' => 'La cuenta de usuario se ha creado correctamente'
            ], 201);

        } catch (BadRequestException $e) {
            return $this->json(['data' => $e->getMessage()], $e->getCode());
        }
    }

    #[Route('/user/login', name: 'app_user')]
    public function login(Request $request, UserRepository $UserRepository, JWTEncoderInterface $jwtEncoder, JWTDecodedEvent $event)
    : JsonResponse
    {
        try {

            $jwtDecodedListener = new JWTDecodedListener();
            
            $payload = $jwtDecodedListener->onJWTDecoded($event);
           

            if (!$user) {
                throw new Exception("Usuario no encontrado");
            }

            $response = array(
                'id' => $user->getId(),
                'name' => $user->getName(),
                'surname' => $user->getSurname(),
                'rol' => $user->getRoles()
            );

            return $this->json([
                'data' => $response
            ], 200);

        } catch(Exception $e) {
            
            return $this->json([
                'data' => $e->getMessage(),
            ], 404);

        }
    }
}
