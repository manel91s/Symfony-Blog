<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TagControllerTest extends UserCredentialsAbstractTest
{
    use RecreateDatabaseTrait;
    
    private const ENDPOINT_TAGS  = '/api/tags';
    private const ENDPOINT_SAVE  = '/api/tags/save';
    private const ENDPOINT_UPDATE  = '/api/tags/update';
    private const ENDPOINT_DELETE = '/api/tags/delete';
  
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * test gest all tags
     */
    public function testGetTags(): void
    {   
        $this->testRegisterUser();
        $this->testLoginCheck();

        self::$client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $this->authToken);

        self::$client->request(Request::METHOD_GET, self::ENDPOINT_TAGS, [], [], [], '');

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    /**
     * test get tag by id
     */
    public function testGetTagById(): void
    {
        $this->testLoginCheck();
        
        self::$client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $this->authToken);

        $parameters = [
            'id' => '1'
        ];

        self::$client->request(Request::METHOD_GET, self::ENDPOINT_TAGS, $parameters, [], [], '');

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }
    
    public function testSaveTag(): void
    {
        $this->testLoginCheck();

        self::$client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $this->authToken);

        $payload = [
            'name' => 'Informatica',
        ];

        self::$client->request(Request::METHOD_POST, self::ENDPOINT_SAVE, [], [], [], json_encode($payload));

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
    }

    public function testUpdateTag(): void
    {
        $this->testLoginCheck();

        self::$client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $this->authToken);

        $payload = [
            'name' => 'Juegos',
        ];

        self::$client->request(Request::METHOD_PUT, self::ENDPOINT_UPDATE . '/4', [], [], [], json_encode($payload));

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    public function testDeleteTag(): void
    {
        $this->testLoginCheck();

        self::$client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $this->authToken);

        $payload = [
            'name' => 'Juegos',
        ];

        self::$client->request(Request::METHOD_DELETE, self::ENDPOINT_DELETE . '/2', [], [], [], json_encode($payload));

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

}
