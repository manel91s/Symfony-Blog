<?php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Services\UserService;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



ini_set('memory_limit', '256M');

class UserController extends AbstractController
{
    private $validator;
    private $em;
    
    public function __construct(ValidatorInterface $validator, EntityManagerInterface $entitymanager)
    {
        $this->validator = $validator;
        $this->em = $entitymanager;
    }

    #[Route('/user/registration', name: 'app_user_create', methods: 'POST')]
    public function registration(Request $request,
    UserRepository $userRepository,
    UserPasswordHasherInterface $passwordHasher) : JsonResponse
    {
        try {

            $user = $userRepository->findBy(
                array('email' => $request->get('email'))
            );
    
            if ($user) {
                throw new Exception('El usuario ya existe');
            }

            $user = new User();
            $user->setName($request->get('name'));
            $user->setSurname($request->get('surname'));
            $user->setEmail($request->get('email'));
            $user->setPassword($request->get('password'));
            $user->setRoles(["USER"]);

            $errors = $this->validator->validate($user);

            if (count($errors) > 0) {
                throw new Exception( (string) $errors);
            }

            $userService = new UserService($this->em);

            $user = $userService->register($user, $passwordHasher);

            if (!$user) {
                throw new Exception('Ha habido un error al crear el usuario');
            }

            return $this->json([
                'data' => 'Usuario registrado correctamente '.$user,
            ], 200);

        } catch (Exception $e) {

            return $this->json(['data' => $e->getMessage()], 404);
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
