<?php 

namespace App\Services;

use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class PostService 
{
    private PostRepository $postRepository;
    private $projectDir;
    public function __construct(PostRepository $postRepository, KernelInterface $kernel)
    {
        $this->postRepository = $postRepository;
        $this->projectDir = $kernel->getProjectDir();
    }

    public function savePost() {

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

    public function get(int $id)
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
        $fileUploader = new FileUploader($this->projectDir . '/public/uploads/profiles');

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