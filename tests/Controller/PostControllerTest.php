<?php

use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PostControllerTest extends WebTestCase
{
    use RecreateDatabaseTrait;
    private const ENDPOINT  = '/api/posts';
    private static ?KernelBrowser $client = null;


    public function setUp(): void
    {
        parent::setUp();

        if (null === self::$client) {
            self::$client = static::createClient();
            self::$client->setServerParameter('CONTENT_TYPE', 'application/json');
        }
    }

    public function testGetPosts(): void
    {
        self::$client->request(Request::METHOD_GET, self::ENDPOINT, [], [], [], '');

        $response = self::$client->getResponse();

        self::assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

}
