<?php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
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
    #[Route('/user/registration', name: 'app_user_create', methods: 'POST')]
    public function registration(ValidatorInterface $validator, Request $request, EntityManagerInterface $entityManager,
    UserPasswordHasherInterface $passwordHasher) : JsonResponse
    {
        try {

            $user = new User();
            $user->setName($request->get('name'));
            $user->setSurname($request->get('surname'));
            $user->setEmail($request->get('email'));
            $user->setRoles(["USER"]);

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $request->get('password')
            );
            $user->setPassword($hashedPassword);
            
            $errors = $validator->validate($user);
            
            if (count($errors) > 0) {
                return $this->json(['data' => (string) $errors], 404);
            }

            $entityManager->persist($user);

            $entityManager->flush();
           
            return $this->json([
                'data' => 'Usuario registrado correctamente',
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
