<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\DBAL\UniqueConstraintViolationException;
// use \Doctrine\ORM\EntityManager;

class UserTest extends KernelTestCase
{
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;
    private $userRepository;
    private $userMock;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        
        $this->userRepository = $this->entityManager->getRepository(User::class);
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

        $newUser = $this->userRepository->store($data['name'], $data['email'], $data['phone'], $data['password']);

        return $newUser;
    }

    // [ UNIT TEST ]
    public function testUStore()
    {
        $user = $this->store();

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame($user->getEmail(), $this->userMock['email']);
    }
    
    // [ INTEGRATION TEST ]
    public function testIStore()
    {
        $user = $this->store();       

        $userFound = $this->userRepository->findByEmail('ronaldo@mail.com');

        $this->assertSame($user, $userFound);
        
        $this->userMock['id'] = $userFound->getId();
        unset($this->userMock['password']);

        $this->assertEquals($this->userMock, $user->toArray());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
