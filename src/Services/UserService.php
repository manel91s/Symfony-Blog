<?php

namespace App\Services;

use App\Entity\User;
use App\Http\DTO\ProfileRequest;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\UserNotFoundException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelInterface;

class UserService 
{
    private UserRepository $userRepository;
    private jwtService $jwtService;
    private FileUploader $fileUploader;
    

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Update the user
     * @return User
     */
    public function updateProfile(ProfileRequest $request): User
    {
        try {

           $bearerToken = $this->jwtService->getTokenFromRequest($request);
           $payload = $this->jwtService->decodeToken($bearerToken);

           if(!$user = $this->checkUserById($payload['userId'])) {
              throw new BadRequestException("Este usuario no existe", Response::HTTP_CONFLICT);
           }

           $user->setName($request->getName());
           $user->setSurname($request->getSurname());
           $user->setEmail($request->getEmail());

           if ($user->getAvatar() && $request->getFile()) {
                $this->fileUploader->remove($user->getAvatar() );
                $fileName = $this->uploadImage($request->getFile());
                $user->setAvatar($fileName);
            }

            $this->userRepository->save($user, true);

        } catch(BadRequestException $e) {
            throw new BadRequestException($e->getMessage(), $e->getCode());
        }

        return $user;
    }

    private function uploadImage(UploadedFile $file)
    {
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

        return $this->fileUploader->upload($file);
    }

    public function checkUser(string $email): ?User
    {
        return $this->userRepository->findOneByEmail($email);
    }

    public function checkUserById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function checkUserByToken(string $token): ?User
    {
        return $this->userRepository->findOneBy(['token' => $token]);
    }

    public function setEncoder(JWTEncoderInterface $jwtEncoder)
    {
        $this->jwtService = new jwtService($jwtEncoder);
    }

    public function setFileUploader(KernelInterface $kernel)
    {
        $this->fileUploader = new FileUploader($kernel->getProjectDir(). '/public/uploads/avatar');
    }
}