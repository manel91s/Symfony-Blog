<?php 

namespace App\Services;

use App\Entity\Post;
use App\Http\DTO\PostRequest;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class PostService 
{
    private PostRepository $postRepository;
    private UserService $userService;
    private $projectDir;
    private jwtService $jwtService;

    public function __construct(
        PostRepository $postRepository,
        UserRepository $userRepository,
        KernelInterface $kernel,
        JWTEncoderInterface $jwtEncoder)
    {
        $this->postRepository = $postRepository;
        $this->userService = new UserService($userRepository);
        $this->jwtService = new jwtService($jwtEncoder);
        $this->projectDir = $kernel->getProjectDir();
    }

    public function save(PostRequest $request): void
    {
        
        $bearerToken = $this->jwtService->getTokenFromRequest($request);
        $payload = $this->jwtService->decodeToken($bearerToken);

        if(!$user = $this->userService->checkUserById($payload['userId'])) {
            throw new BadRequestException("Este email no está registrado", Response::HTTP_CONFLICT);
        }
        
        $post = new Post(
            $request->getTitle(),
            $request->getBody(),
            $user
        );

        if ($request->getImage()) {
            $fileName = $this->uploadImage($request->getImage());
            $post->setImage($fileName);
        }
        
        $this->postRepository->save($post, true);
    }

    public function update(PostRequest $request): void
    {
        $bearerToken = $this->jwtService->getTokenFromRequest($request);
        $payload = $this->jwtService->decodeToken($bearerToken);

        if(!$this->userService->checkUserById($payload['userId'])) {
            throw new BadRequestException("Este email no está registrado", Response::HTTP_CONFLICT);
        }

        if(!$post = $this->postRepository->find($request->getId())) {
            throw new BadRequestException(
                "El post que intentas editar no ha sido encontrado", Response::HTTP_NOT_FOUND
            );
        }

        if($post->getUser()->getId() !== $payload['userId']) {
            throw new BadRequestException(
                "El post que intentas actualizar no pertenece ha este usuario", Response::HTTP_CONFLICT
            );
        }

        $post->setTitle($request->getTitle());
        $post->setBody($request->getBody());

        if ($request->getImage()) {
            $fileName = $this->uploadImage($request->getImage());
            $post->setImage($fileName);
        }
        
        $this->postRepository->save($post, true);
    }

    public function delete(PostRequest $request): void
    {
        $bearerToken = $this->jwtService->getTokenFromRequest($request);
        $payload = $this->jwtService->decodeToken($bearerToken);

        if(!$this->userService->checkUserById($payload['userId'])) {
            throw new BadRequestException("El usuario no existe", Response::HTTP_CONFLICT);
        }

        if(!$post = $this->postRepository->find($request->getId())) {
            throw new BadRequestException(
                "El post que intentas editar no ha sido encontrado", Response::HTTP_NOT_FOUND
            );
        }

        if($post->getUser()->getId() !== intval($payload['userId'])) {
            throw new BadRequestException(
                "El post que intentar eliminar no pertenece ha este usuario", Response::HTTP_CONFLICT
            );
        }

        $this->postRepository->remove($post, true);
    }

    public function getPosts(): array
    {
        $posts = $this->postRepository->findAll();

        $arrayPosts = [];
        foreach ($posts as $post) {
            $arrayPosts[] = [
                'id' => $post->getId(),
                'title' => $post->getTitle(),
                'image' => $post->getImage(),
                'user' => [
                    'id' => $post->getUser()->getId(),
                    'name' => $post->getUser()->getName(),
                    'email' => $post->getUser()->getEmail(),
                ]
            ];
        }

        return $arrayPosts;
    }

    public function get(int $id) : array
    {
        $post = $this->postRepository->find($id);

        if(!$post) {
            throw new BadRequestException("No existe la publicación", Response::HTTP_NOT_FOUND);
        }

        return [
            'id' => $post->getId(),
            'title' => $post->getTitle(),
            'image' => $post->getImage(),
            'user' => [
                'id' => $post->getUser()->getId(),
                'name' => $post->getUser()->getName(),
                'email' => $post->getUser()->getEmail(),
            ]
        ];
    }

    private function uploadImage(UploadedFile $file)
    {
        $fileUploader = new FileUploader($this->projectDir . '/public/uploads/posts');

        try {
            
            if($file->getSize() > 1000000) {
                throw new BadRequestException("El tamaño de la imagen no puede ser mayor a 1MB", Response::HTTP_BAD_REQUEST);
            }

            if(!in_array($file->getMimeType(), ['image/jpeg', 'image/png'])) {
                throw new BadRequestException("El formato de la imagen no es válido", Response::HTTP_BAD_REQUEST);
            }
            
        } catch (BadRequestException $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $fileUploader->upload($file);
    }


}