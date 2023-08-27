<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PostControllerTest extends UserCredentialsAbstractTest
{
    use RecreateDatabaseTrait;
    
    private const ENDPOINT_POSTS  = '/api/posts';
    private const ENDPOINT_SAVE = '/api/post/save';
  
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * test gest all posts
     */
    public function testGetPosts(): void
    {   
        $this->testRegisterUser();
        $this->testLoginCheck();

        self::$client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $this->authToken);

        self::$client->request(Request::METHOD_GET, self::ENDPOINT_POSTS, [], [], [], '');

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    /**
     * test get post by id
     */
    public function testGetPostById(): void
    {
        $this->testLoginCheck();
        
        self::$client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $this->authToken);

        self::$client->request(Request::METHOD_GET, self::ENDPOINT_POSTS . '/1', [], [], [], '');

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    public function testSavePostWithNoTitle(): void
    {
        $this->testLoginCheck();

        self::$client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $this->authToken);

        $payload = [
            'title' => '',
            'body' => 'Esto es el cuerpo del post',
            'image' => 'directory_path'
        ];

        self::$client->request(Request::METHOD_POST, self::ENDPOINT_SAVE, [], [], [], json_encode($payload));

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testSavePostWithNoBody(): void
    {
        $this->testLoginCheck();

        self::$client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $this->authToken);

        $payload = [
            'title' => 'Blog personal Manel',
            'body' => '',
            'image' => 'directory_path'
        ];

        self::$client->request(Request::METHOD_POST, self::ENDPOINT_SAVE, [], [], [], json_encode($payload));

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testSavePost(): void
    {
        $this->testLoginCheck();

        self::$client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $this->authToken);

        $payload = [
            'title' => 'Blog Personal Manel',
            'body' => 'Esto es el cuerpo del post'
        ];

        self::$client->request(Request::METHOD_POST, self::ENDPOINT_SAVE, [], [], [], json_encode($payload));

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
    }

}
