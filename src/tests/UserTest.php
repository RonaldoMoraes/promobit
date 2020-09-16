<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\DBAL\UniqueConstraintViolationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;

class FakeUser implements UserInterface
{
    public function getRoles(){}
    public function getPassword(){}
    public function getSalt(){}
    public function getUsername(){}
    public function eraseCredentials(){}
}

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

        $newUser = $this->userRepository->store($data);

        return $newUser;
    }

    public function testSetRoles()
    {
        $user = $this->store();
        // $user = $this->userRepository->show($user['id']);
        $roles = $user->setRoles(['TEST']);
        // dd($roles->getRoles());
        $this->assertEquals(['TEST', 'ROLE_USER'], $roles->getRoles());
    }

    // public function testGetRoles()
    // {
    //     $user = $this->store();
    //  //   $user = $this->userRepository->show($user['id']);
    //     $roles = $user->getRoles();

    //     $this->assertEquals(['ROLE_USER'], $roles);
    // }

    public function testUpgradePassword()
    {
        $user = $this->store();
        // $user = $this->userRepository->listAll()[0];
        // $user = $this->userRepository->show($user['id']);

        $condition = $this->userRepository->upgradePassword($user, '123123');
        $this->assertFalse(!!$condition);

        $fake = new FakeUser();
        $this->expectException(UnsupportedUserException::class);
        $this->userRepository->upgradePassword($fake, '123123');
    }
    
    public function testShow()
    {
        $userFirst = $this->store();
        // $users = $this->userRepository->listAll();
        $user = $this->userRepository->show($userFirst->getId())->toArray();
        // dd($users);
        $this->assertEquals($userFirst->getEmail(), $user['email']);
    }
    
    public function testListAll()
    {
        $this->store();
        $user = $this->userMock;
        $users = $this->userRepository->listAll();
        // dd($users);
        $this->assertEquals($user['email'], $users[0]['email']);
    }

    // [ INTEGRATION TEST ]
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

    // [ INTEGRATION TEST ]
    public function testIShow()
    {
        // Store user and get it's values
        $user = $this->store();

        // Find that same user by it's ID
        $userFound = $this->userRepository->find($user->getId());

        $this->assertSame($user, $userFound);
    }

    // [ INTEGRATION TEST ]
    public function testIUpdate()
    {
        // Store user and get it's values
        $user = $this->store();

        $newDataMock = [
            'name' =>   'Ronaldinho',
            'email' =>   'ronaldinho@mail.com',
            'phone' =>   '35991458401',
            'password' =>   'aloalo123'
        ];

        // TO DO: Assert $userUpdated is true
        $userUpdated = $this->userRepository->update($user, $newDataMock);

        $userUpdatedArray = $this->userRepository->find($user->getId())->toArray();
        unset($newDataMock['password']);
        unset($userUpdatedArray['id']);

        $this->assertEquals($newDataMock, $userUpdatedArray);
    }

    // [ INTEGRATION TEST ]
    public function testIDelete()
    {
        // Store user and get it's values
        $user = $this->store();
        $userId = $user->getId();

        $deteled = $this->userRepository->delete($user);

        $notFound = $this->userRepository->find($userId);

        $this->assertTrue($deteled);
        $this->assertNull($notFound);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
