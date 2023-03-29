<?php

use App\Entity\User;
use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserControllerTest extends WebTestCase {

    private const ENDPOINT  = '/api/user/registration';

    private static ?KernelBrowser $client = null;
    

    public function setUp(): void
    {
        parent::setUp();

        if(null === self::$client) {
            self::$client = static::createClient();
            self::$client->setServerParameter('CONTENT_TYPE', 'application/json');
        }

    }

    public function testRegisterUser(): void
    {
        
        $payload = [
            'name' => 'Manel',
            'surname' => 'Aguilera',
            'email' => 'manel@api.com',
            'password' => 'password123'
        ];

        self::$client->request(Request::METHOD_POST, self::ENDPOINT, [], [], [], json_encode($payload));

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
    }

   public function testRegisterUserWithNoName(): void
    {
        
        $payload = [
            'email' => 'manel@api.com',
            'password' => 'password123'
        ];

        self::$client->request(Request::METHOD_POST, self::ENDPOINT, [], [], [], json_encode($payload));

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());

    }

    public function testRegisterUserWithNoSurname(): void
    {
        
        $payload = [
            'name' => 'Manel',
            'email' => 'manel@api.com',
            'password' => 'password123'
        ];

        self::$client->request(Request::METHOD_POST, self::ENDPOINT, [], [], [], json_encode($payload));

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());

    }

    
   public function testRegisterUserWithNoEmail(): void
   {
       
       $payload = [
           'name' => 'Manel',
           'surname' => 'Aguilera',
           'password' => 'password123'
       ];

       self::$client->request(Request::METHOD_POST, self::ENDPOINT, [], [], [], json_encode($payload));

       $response = self::$client->getResponse();

       self::assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());

   }

   public function testRegisterUserWithNoPassword(): void
   {
       
       $payload = [
           'name' => 'Manel',
           'surname' => 'Aguilera',
           'email' => 'manel@api.com',
       ];

       self::$client->request(Request::METHOD_POST, self::ENDPOINT, [], [], [], json_encode($payload));

       $response = self::$client->getResponse();

       self::assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
   }
}   