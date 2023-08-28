<?php

namespace App\Tests\Controller;

use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Abstract class to test user credentials (We recover token for each request)
 */
abstract class UserCredentialsAbstractTest extends WebTestCase
{
    use RecreateDatabaseTrait;
    private const ENDPOINT  = '/api/user/registration';
    private const ENDPOINT_CHECK  = '/api/login_check';
    protected static ?KernelBrowser $client = null;
    protected string $authToken = '';

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
    protected function testRegisterUser(): void
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
    protected function testLoginCheck(): void
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
    
    protected function getAuthToken(): string
    {
        return $this->authToken;
    }

}
