<?php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Services\UserService;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



ini_set('memory_limit', '256M');

class UserController extends AbstractController
{
    private $validator;
    private $userRepository;
    
    public function __construct(ValidatorInterface $validator, UserRepository $userRepository)
    {
        $this->validator = $validator;
        $this->userRepository = $userRepository;
    }

    #[Route('/user/registration', name: 'app_user_create', methods: 'POST')]
    public function registration(Request $request,
    UserRepository $userRepository,
    UserPasswordHasherInterface $passwordHasher) : JsonResponse
    {
        try {

            $userService = new UserService($userRepository);

            $data = json_decode($request->getContent(), associative: true);
            
            if(!array_key_exists('name', $data)) {
                throw new BadRequestException('name is mandatory', 400);
            }

            if(!array_key_exists('surname', $data)) {
                throw new BadRequestException('surname is mandatory', 400);
            }

            if(!array_key_exists('email', $data)) {
                throw new BadRequestException('email is mandatory', 400);
            }

            if(!array_key_exists('password', $data)) {
                throw new BadRequestException('password is mandatory', 400);
            }

            $user = new User(
                $data['name'],
                $data['surname'],
                $data['email'],
                $data['password'],
                ['USER']
            );
            $hashedPassword = $userService->hassPassword($user, $passwordHasher);
            $user->setPassword($hashedPassword);
            $user->setToken($userService->generateToken());

            $userRepository->save($user, true);

            if ($user->getId()) {
                $userService->sendEmail($user);
            }

            return $this->json([
                'data' => $user->getToken(),
            ], 201);

        } catch (BadRequestException $e) {
            return $this->json(['data' => $e->getMessage()], $e->getCode());
        }
    }

    #[Route('/user/login', name: 'app_user')]
    public function login(Request $request, UserRepository $UserRepository, JWTEncoderInterface $jwtEncoder)
    : JsonResponse
    {
        try {

            $requestToken = $request->headers->get('authorization');

            $tokenDecode = $jwtEncoder->decode(substr($request->headers->get('authorization'), 6, strlen($requestToken)));

            $user = $UserRepository->findOneBy(
                array('email' => $tokenDecode['username'])
            );

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
