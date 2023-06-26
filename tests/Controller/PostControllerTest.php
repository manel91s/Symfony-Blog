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
    
    private const ENDPOINT  = '/api/posts';
  
    public function setUp(): void
    {
        parent::setUp();

        if (null === self::$client) {
            self::$client = static::createClient();
            self::$client->setServerParameter('CONTENT_TYPE', 'application/json');
        }
    }

    /**
     * test gest all posts
     */
    public function testGetPosts(): void
    {
        $this->testRegisterUser();
        $this->testLoginCheck();

        self::$client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $this->getAuthToken());

        self::$client->request(Request::METHOD_GET, self::ENDPOINT, [], [], [], '');

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

}
