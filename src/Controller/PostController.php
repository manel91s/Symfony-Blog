<?php

namespace App\Controller;

use App\Services\PostService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/posts', name: 'app_post', methods: 'GET')]
    public function index(PostService $postService): JsonResponse
    {
        try {

            $posts = $postService->getPosts();

            return $this->json([
                'data' => $posts
            ], 200);
        } catch (BadRequestException $e) {
            return $this->json(['data' => "No hay publicaciones"], $e->getCode());
        }
    }
}
