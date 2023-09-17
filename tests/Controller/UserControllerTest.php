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
    private const ENDPOINT_UPDATE  = '/api/user/update';
    private const ENDPOINT_UPDATE_PROFILE  = '/api/user/update/profile';
    private const ENDPOINT_UPDATE_PASSWORD  = '/api/user/update/password';
    private const ENDPOINT_LOGIN  = '/api/user/login';
    private const ENDPOINT_ACTIVATE  = '/api/user/activate';
    private const ENDPOINT_CHECK  = '/api/login_check';
    private const ENDPOINT_FORGOT_PASSWORD  = '/api/user/forgot-password';
    private static ?KernelBrowser $client = null;
    private string $authToken = '';
    private string $tokenUserBD = '';

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

        self::$client->request(Request::METHOD_GET, self::ENDPOINT_ACTIVATE. '/token=e236416c05d3a05d8b456d83370ac0acbd6a8811', [], [], [], json_encode($payload));

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testActivateUserWithTokenSuccess(): void
    {
        $payload = [
            'token' => '7cd4fdff95377f252ce04951833be4e2d5412d36'
        ];

        self::$client->request(Request::METHOD_GET, self::ENDPOINT_ACTIVATE. '/token=7cd4fdff95377f252ce04951833be4e2d5412d36', [], [], [], json_encode($payload));

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
    public function testLoginCheck(string $newPassword = ''): void
    {
        $payload = [
            'username' => 'manel.aguilera91@gmail.com',
            'password' => empty($newPassword) ? '123456' : $newPassword
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

    /**
     * test the update of a user
     */
    public function testUpdateUserPassword(): void
    {
        $this->testLoginCheck();

        self::$client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $this->authToken);

        $payload = [
            'oldPassword' => '123456',
            'newPassword' => '654321',
        ];

        self::$client->request(Request::METHOD_PATCH, self::ENDPOINT_UPDATE_PASSWORD, [], [], [], json_encode($payload));

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

    }

    /**
     * test the update of a user
     */
    public function testUpdateProfileUser(): void
    {
        $newPassword = '654321';

        $this->testLoginCheck($newPassword);

        self::$client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $this->authToken);

        $payload = [
            'name' => 'Manel ACTUALIZADO',
            'surname' => 'Aguilera ACTUALIZADO',
            'email' => 'maneliko.hcc@gmail.com'
        ];

        self::$client->request(Request::METHOD_POST, self::ENDPOINT_UPDATE_PROFILE, [], [], [], json_encode($payload));

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

    }

    /**
     * test reset token of a user
     */
    public function testForgotPassword(): void
    {
        $payload = [
            'email' => 'maneliko@msn.com'
        ];

        self::$client->request(Request::METHOD_PATCH, self::ENDPOINT_FORGOT_PASSWORD, [], [], [], json_encode($payload));

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    /**
     * test reset token of a user
     */
    public function testRestorePassword(): void
    {
        $payload = [
            'email' => 'maneliko@msn.com'
        ];

        self::$client->request(Request::METHOD_PATCH, self::ENDPOINT_FORGOT_PASSWORD, [], [], [], json_encode($payload));

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }
    
    public function getAuthToken(): string
    {
        return $this->authToken;
    }

}
