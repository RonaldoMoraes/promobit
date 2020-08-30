<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\DBAL\UniqueConstraintViolationException;

class UserTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;
    private $userRepository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    public function testStore()
    {
        $user = $this->userRepository->store('Ronaldo', 'ronaldo@mail.com', '123123123', 'password123');        
        $userFound = $this->userRepository->findByEmail('ronaldo@mail.com');
        
        $this->assertObjectHasAttribute('name', $user);
        $this->assertSame($user, $userFound);
    }

    public function testUserConvertedToArray()
    {
        $user = $this->userRepository->store('Ronaldo', 'ronaldo@mail.com', '123123123', 'password123')->toArray();        
        unset($user['id']);
        
        $this->assertSame(array(
            'name' => 'Ronaldo',
            'email' => 'ronaldo@mail.com',
            'phone' => '123123123'
        ), $user);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
