<?php

namespace App\Controller;

use App\Http\DTO\TagRequest;
use App\Services\TagService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends AbstractController
{

    #[Route('/tags/save', name: 'app_tag_save', methods: 'POST')]
    public function saveTag(TagRequest $request, TagService $tagService): JsonResponse
    {
        try {

            $tagService->save($request);

            return $this->json( [ 'msg' => 'El Tag se ha registrado correctamente' ], 201);
            
        } catch (BadRequestException $e) {
            return $this->json(['msg' => $e->getMessage()], $e->getCode());
        }
    }

    #[Route('/tags/update/{id}', name: 'app_tag_update', methods: 'PUT')]
    public function updateTag(TagRequest $request, TagService $tagService): JsonResponse
    {
        try {

            $tagService->update($request);

            return $this->json( [ 'msg' => 'El tag se ha actualizado correctamente' ], 200);
            
        } catch (BadRequestException $e) {
            return $this->json(['msg' => $e->getMessage()], $e->getCode());
        }
    }

    #[Route('/tags/delete/{id}', name: 'app_tag_delete', methods: 'DELETE')]
    public function deleteTag(TagRequest $request, TagService $tagService): JsonResponse
    {
        try {

            $tagService->delete($request);

            return $this->json( [ 'msg' => 'El tag se ha eliminado correctamente' ], 200);
            
        } catch (BadRequestException $e) {
            return $this->json(['msg' => $e->getMessage()], $e->getCode());
        }
    }

    #[Route('/tags/{id}', name: 'app_tag_id', methods: 'GET')]
    public function getTag(TagRequest $request, TagService $tagService): JsonResponse
    {
        try {

            $tag = $tagService->get($request->getId());

            return $this->json( [ 'data' => $tag ], 200);
            
        } catch (BadRequestException $e) {
            return $this->json(['msg' => $e->getMessage()], $e->getCode());
        }
    }
    
    #[Route('/tags', name: 'app_tags', methods: 'GET')]
    public function getTags(TagService $tagService): JsonResponse
    {
        try {

            $tags = $tagService->getTags();

            return $this->json($tags , 200);
            
        } catch (BadRequestException $e) {
            return $this->json(['msg' => $e->getMessage()], $e->getCode());
        }
    }


}
