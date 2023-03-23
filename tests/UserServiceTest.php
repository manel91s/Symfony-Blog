<?php

use App\Entity\User;
use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserServiceTest extends KernelTestCase {

    private MockObject|EntityManagerInterface $repository;
    private MockObject|UserPasswordHasherInterface $userHashPassword;
    private UserService $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->userHashPassword = $this->getMockBuilder(UserPasswordHasherInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->user = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new UserService($this->repository);
    }

    public function testRegister(): void
    {
        $user = new User();
        $user->setName('Aitor');
        $user->setSurname('Guerrero');
        $user->setEmail('aitor@aitor@msn.com');
        $user->setPassword('123456');
        $user->setRoles(["USER"]);

        $this->$userHashPassword = $this->getMockBuilder(UserPasswordHasherInterface::class)
        ->disableOriginalConstructor()
        ->getMock();

        $user = $this->service->register($user, $this->$userHashPassword);

        $this->assertNotNull($user->getId());
    }
}   