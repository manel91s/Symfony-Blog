<?php

namespace App\Services;

use App\Entity\Tag;
use App\Http\DTO\TagRequest;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;

class TagService
{
    private TagRepository $tagRepository;
    private UserService $userService;
    private jwtService $jwtService;
    public function __construct(
        TagRepository $tagRepository,
        UserRepository $userRepository,
        JWTEncoderInterface $jwtEncoder
    ) {
        $this->tagRepository = $tagRepository;
        $this->jwtService = new jwtService($jwtEncoder);
        $this->userService = new UserService($userRepository);
    }

    public function getTags(): array
    {
        if (!$tags = $this->tagRepository->findAll()) {
            throw new BadRequestException("Este email no está registrado", Response::HTTP_NOT_FOUND);
        }

        $arrayTags = [];
        foreach ($tags as $tag) {
            $arrayTags[] = [
                'id' => $tag->getId(),
                'name' => $tag->getName()
            ];
        }
        return $arrayTags;
    }

    public function get(int $id): Tag
    {
        return $this->tagRepository->findOneBy($id);
    }

    public function save(TagRequest $request): void
    {
        $bearerToken = $this->jwtService->getTokenFromRequest($request);
        $payload = $this->jwtService->decodeToken($bearerToken);

        if (!$user = $this->userService->checkUserById($payload['userId'])) {
            throw new BadRequestException("Este email no está registrado", Response::HTTP_CONFLICT);
        }

        $tag = new Tag($request->getName());

        $this->tagRepository->save($tag, true);
    }

    public function update(TagRequest $request): void
    {
        $bearerToken = $this->jwtService->getTokenFromRequest($request);
        $payload = $this->jwtService->decodeToken($bearerToken);

        if (!$this->userService->checkUserById($payload['userId'])) {
            throw new BadRequestException("Este email no está registrado", Response::HTTP_CONFLICT);
        }

        if (!$tag = $this->tagRepository->find($request->getId())) {
            throw new BadRequestException(
                "El tag que intentas editar no ha sido encontrado",
                Response::HTTP_NOT_FOUND
            );
        }

        $tag->setName($request->getName());

        $this->tagRepository->save($tag, true);
    }

    public function delete(TagRequest $request): void
    {
        $bearerToken = $this->jwtService->getTokenFromRequest($request);
        $payload = $this->jwtService->decodeToken($bearerToken);

        if (!$this->userService->checkUserById($payload['userId'])) {
            throw new BadRequestException("Este email no está registrado", Response::HTTP_CONFLICT);
        }

        if (!$tag = $this->tagRepository->find($request->getId())) {
            throw new BadRequestException(
                "El tag que intentas editar no ha sido encontrado",
                Response::HTTP_NOT_FOUND
            );
        }

        $this->tagRepository->remove($tag, true);
    }
}
