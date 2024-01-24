<?php

namespace App\Controller;

use App\Http\DTO\CheckProfileRequest;
use App\Http\DTO\PostRequest;
use App\Services\PostService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{

    #[Route('/post/save', name: 'app_post_save', methods: 'POST')]
    public function savePost(
        PostRequest $request, 
        PostService $postService): JsonResponse
    {
        try {
            $postService->save($request);

            return $this->json([
                'msg' => 'El post se ha registrado correctamente'
            ], 201);

        } catch(BadRequestException $e) {
            return $this->json(['msg' => $e->getMessage()], $e->getCode());
        }

        return $this->json([], JsonResponse::HTTP_CREATED);
    }

   #[Route('/post/update/{id}', name: 'app_post_update', methods: 'POST')]
    public function updatePost(PostRequest $request, PostService $postService): JsonResponse
    {
       try {
            $postService->update($request);

            return $this->json([
                'msg' => 'El post se ha actualizado correctamente'
            ], 200);

        } catch(BadRequestException $e) {
            return $this->json(['msg' => $e->getMessage()], $e->getCode());
        }
    }

    #[Route('/post/delete/{id}', name: 'app_post_delete', methods: 'DELETE')]
    public function deletePost(PostRequest $request, PostService $postService): JsonResponse
    {
       try {
            $postService->delete($request);

            return $this->json([
                'msg' => 'El post se ha eliminado correctamente'
            ], 200);

        } catch(BadRequestException $e) {
            return $this->json(['msg' => $e->getMessage()], $e->getCode());
        }
    }

    #[Route('/posts', name: 'app_post', methods: 'GET')]
    public function getPosts(PostService $postService): JsonResponse
    {
        try {

            $posts = $postService->getPosts();

            return $this->json([
                'data' => $posts
            ], 200);
        } catch (BadRequestException $e) {
            return $this->json(['msg' => "No hay publicaciones"], $e->getCode());
        }
    }

    #[Route('/posts/user', name: 'app_post', methods: 'GET')]
    public function getUserPosts(CheckProfileRequest $request, PostService $postService): JsonResponse
<<<<<<< HEAD
=======
    {
        try {

            $posts = $postService->getUserPosts($request);

            return $this->json($posts, 200);
        } catch (BadRequestException $e) {
            return $this->json(['msg' => "No hay publicaciones"], $e->getCode());
        }
    }

    #[Route('/posts/{id}', name: 'app_post_id', methods: 'GET')]
    public function getPost(PostRequest $request, PostService $postService): JsonResponse
>>>>>>> 9319cf7c2f4bae067dc306b6f8d11c5900a019aa
    {
        try {

            $posts = $postService->getUserPosts($request);

            return $this->json($posts, 200);
        } catch (BadRequestException $e) {
            return $this->json(['msg' => "No hay publicaciones"], $e->getCode());
        }
    }

    #[Route('/posts/{id}', name: 'app_post_id', methods: 'GET')]
    public function getPost(Request $request, PostService $postService): JsonResponse
    {
        try {

            $post = $postService->get($request->attributes->get('id'));
            
            return $this->json($post, 200);
        } catch (BadRequestException $e) {
            return $this->json(['msg' => $e->getMessage()], $e->getCode());
        }
    }
}
