<?php

namespace App\Controller;

use App\Http\DTO\PostRequest;
use App\Services\PostService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{

    #[Route('/post/save', name: 'app_post_save', methods: 'POST')]
    public function savePost(PostRequest $request, PostService $postService): JsonResponse
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

   #[Route('/post/update/{id}', name: 'app_post_update', methods: 'PUT')]
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

    #[Route('/posts/{id}', name: 'app_post_id', methods: 'GET')]
    public function getPost(PostRequest $request, PostService $postService): JsonResponse
    {
        try {

            $post = $postService->get($request->getId());

            return $this->json([
                'data' => $post
            ], 200);
        } catch (BadRequestException $e) {
            return $this->json(['msg' => $e->getMessage()], $e->getCode());
        }
    }
}
