<?php

use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class LoginControllerTest extends WebTestCase
{
    
    use RecreateDatabaseTrait;
    private const ENDPOINT  = '/api/user/registration';

    private static ?KernelBrowser $client = null;


    public function setUp(): void
    {
        parent::setUp();

        if (null === self::$client) {
            self::$client = static::createClient();
            self::$client->setServerParameter('CONTENT_TYPE', 'application/json');
            self::$client->setServerParameter('HTTP_Authorization', 'Bearer');
        }
    }

    /**
     * check if the name does not arrive
     */
    public function testLoginUserWithNoEmail(): void
    {

        $payload = [
            'password' => 'password123'
        ];

        self::$client->request(Request::METHOD_POST, self::ENDPOINT, [], [], [], json_encode($payload));

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());

    }

    /**
     * check if the password does not arrive
     */
    public function testLoginUserWithNoPassword(): void
    {
        $payload = [
            'email' => 'manel@api.com'
        ];

        self::$client->request(Request::METHOD_POST, self::ENDPOINT, [], [], [], json_encode($payload));

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());

    }

}