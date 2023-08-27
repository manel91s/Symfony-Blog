<?php

namespace App\Tests\Controller;

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
    private const ENDPOINT_CHECK  = '/api/login_check';
    private static ?KernelBrowser $client = null;
    private string $authToken = '';

    public function setUp(): void
    {
        parent::setUp();

        if (null === self::$client) {
            self::$client = static::createClient();
            self::$client->setServerParameter('CONTENT_TYPE', 'application/json');
        }
    }

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

    /**
     * test the registration of a user
     */
    public function testRegisterUser(): void
    {
        $payload = [
            'name' => 'Manel',
            'surname' => 'Aguilera',
            'email' => 'manel.aguilera91@gmail.com',
            'password' => '123456'
        ];

        self::$client->request(Request::METHOD_POST, self::ENDPOINT, [], [], [], json_encode($payload));

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
    }


    /**
     * test login check to get JWT 
     */
    public function testLoginCheck(): void
    {
        $payload = [
            'username' => 'manel.aguilera91@gmail.com',
            'password' => '123456'
        ];

        self::$client->request(Request::METHOD_POST, self::ENDPOINT_CHECK, [], [], [], json_encode($payload));

        $response = self::$client->getResponse();

        $this->authToken = json_decode($response->getContent())->token;

        self::assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    /**
     * Test that the user is not activated by sending the jwt by header
     */
    public function testLoginNoActiveUser() :void
    {
        $this->testLoginCheck();

        self::$client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $this->getAuthToken());

        $payload = [
            'email' => 'manel.aguilera91@gmail.com',
            'password' => '123456'
        ];

        self::$client->request(Request::METHOD_POST, self::ENDPOINT_LOGIN, [], [], [], json_encode($payload));

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
    
    public function getAuthToken(): string
    {
        return $this->authToken;
    }

}
