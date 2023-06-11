<?php

use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserControllerTest extends WebTestCase
{
    use RecreateDatabaseTrait;
    private const ENDPOINT  = '/api/user/registration';
    private const ENDPOINT_LOGIN  = '/api/user/login';
    private const ENDPOINT_ACTIVATE  = '/api/user/activate';
    private static ?KernelBrowser $client = null;


    public function setUp(): void
    {
        parent::setUp();

        if (null === self::$client) {
            self::$client = static::createClient();
            self::$client->setServerParameter('CONTENT_TYPE', 'application/json');
        }
    }

    /**
     * test the registration of a user
     */
    /*public function testRegisterUser(): void
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
    }*/

    /**
     * check if the name does not arrive
     */
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

    /**
     * Check if the last name does not arrive
     */
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

    /**
     * Check if the email does not arrive
     */
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

    /**
     * Check if the password does not arrive
     */
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

        /**
     * check if the name does not arrive
     */
    public function testLoginUserWithNoEmail(): void
    {

        $payload = [
            'password' => 'password123'
        ];

        self::$client->request(Request::METHOD_POST, self::ENDPOINT_LOGIN, [], [], [], json_encode($payload));

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

        self::$client->request(Request::METHOD_POST, self::ENDPOINT_LOGIN, [], [], [], json_encode($payload));

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testActivateUserWithTokenFailed(): void
    {
        $payload = [
            'token' => 'e236416c05d3a05d8b456d83370ac0acbd6a8811'
        ];

        self::$client->request(Request::METHOD_GET, self::ENDPOINT_ACTIVATE, [], [], [], json_encode($payload));

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testActivateUserWithTokenSuccess(): void
    {
        $payload = [
            'token' => '7cd4fdff95377f252ce04951833be4e2d5412d36'
        ];

        self::$client->request(Request::METHOD_GET, self::ENDPOINT_ACTIVATE, [], [], [], json_encode($payload));

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

}
