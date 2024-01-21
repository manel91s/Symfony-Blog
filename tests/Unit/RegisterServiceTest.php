<?php

namespace App\Tests\Unit;

use App\Entity\User;
use App\Http\DTO\RegisterRequest;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Services\RegisterService;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;

class RegisterServiceTest extends TestCase
{
  private UserRepository $userRepository;
  private UserPasswordHasherInterface $passwordHasher;
  private EntityManagerInterface $entityManager;
  private KernelInterface $kernel;
  private RegisterRequest $registerRequest;
  private RegisterService $registerService;

  public function setUp(): void
  {
    $this->userRepository = $this->createMock(UserRepository::class);
    $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
    $this->entityManager = $this->createMock(EntityManagerInterface::class);
    $this->kernel = $this->createMock(KernelInterface::class);

    $headers = new HeaderBag();
    $headers->set('Authorization', 'Bearer 123456');

    $request = new Request();
    $request->request->set('name', 'manel');
    $request->request->set('surname', 'Aguilera');
    $request->request->set('email', 'manel@msn.com');
    $request->request->set('password', '12345678');
    $request->headers = $headers;

    $this->registerRequest = new RegisterRequest($request);
    $this->registerService = new RegisterService(
      $this->userRepository,
      $this->passwordHasher,
      $this->entityManager,
      $this->kernel
    );
  }

  public function testRegisterUser()
  {
    $user = new User(
      $this->registerRequest->getName(),
      $this->registerRequest->getSurname(),
      $this->registerRequest->getEmail(),
      $this->registerRequest->getPassword(),
      ['ADMIN'],
      '',
    );

    $this->userRepository->expects($this->once())
      ->method('findOneByEmail')
      ->with($this->registerRequest->getEmail())
      ->willReturn(null);

    $this->passwordHasher->expects($this->once())
      ->method('hashPassword')
      ->with($this->isInstanceOf(User::class), $this->registerRequest->getPassword())
      ->willReturn('12345678');

    $this->userRepository->expects($this->once())
      ->method('save')
      ->with($this->isInstanceOf(User::class));

    $userRegistered = $this->registerService->registerUser($this->registerRequest);

    $this->assertInstanceOf(User::class, $userRegistered);
  }
}
