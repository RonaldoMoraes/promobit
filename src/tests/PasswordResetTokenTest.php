<?php

namespace App\Tests;

use App\Entity\ResetPasswordRequest;
use PHPUnit\Framework\TestCase;
use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\DBAL\UniqueConstraintViolationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;

class PasswordResetTokenTest extends KernelTestCase
{
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;
    private $userRepository;
    private $passwordRepository;
    private $userMock;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->passwordRepository = $this->entityManager->getRepository(ResetPasswordRequest::class);
        $this->userMock = array(
            'name'      => 'Ronaldo',
            'email'     => 'ronaldo@mail.com',
            'phone'     => '123123123',
            'password'  => 'password123'
        );
    }

    private function store(?array $user = null)
    {
        $data = $user ?? $this->userMock;

        $newUser = $this->userRepository->store($data);

        return $newUser;
    }

    public function testInit()
    {
        $user = $this->store();
        $pass = new ResetPasswordRequest($user, new DateTime(), '', 'token');
        $this->assertEmpty($pass->getId());
        $this->assertNotEmpty($pass->getUser());
        // $user = $this->userRepository->listAll()[0];
        // $user = $this->userRepository->show($user['id']);

        // $condition = $this->userRepository->upgradePassword($user, '123123');
        // $this->assertFalse(!!$condition);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
