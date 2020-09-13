<?php

namespace App\Tests;

use App\Document\Token;
use PHPUnit\Framework\TestCase;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\DBAL\UniqueConstraintViolationException;
use Doctrine\ODM\MongoDB\DocumentManager;

class JwtTest extends KernelTestCase
{
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $dm;
    private $tokenRepository;
    private $sessionUtil;
    // private $jwtAuthenticator;
    private $tokenMock;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->dm = $kernel->getContainer()
            ->get('doctrine_mongodb')
            ->getManager();
        
        $this->tokenRepository = $this->dm->getRepository(Token::class);
        $this->sessionUtil = self::$container->get('App\Util\SessionUtil');
        // $this->jwtAuthenticator = self::$container->get('App\Security\JwtAuthenticator');
        $this->tokenMock = array(
            'userId'    => 2,
            'email'     => 'ronaldo@mail.com',
            'key'       => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjoicm9uYWxkb0BtYWlsLmNvbSIsImV4cCI6MTU5OTk0MjEzMn0.BASHdWA-U1GSu_T86M0U3bHxgkXNpOUTVXq6EK0gLj8'
        );
    }

    private function store(?array $token = null)
    {
        $data = $token ?? $this->tokenMock;

        $newToken = $this->tokenRepository->store($data);

        return $newToken;
    }

    // [ INTEGRATION TEST ]
    public function testUStore()
    {
        $token = $this->store();

        $this->assertInstanceOf(Token::class, $token);
        $this->assertSame($token->getEmail(), $this->tokenMock['email']);
    }
    
    // [ INTEGRATION TEST ]
    public function testIStore()
    {
        $token = $this->store();       

        $tokenFound = $this->tokenRepository->findLatestByEmail('ronaldo@mail.com');

        $this->assertSame($token, $tokenFound);
        
        $this->tokenMock['id'] = $tokenFound->getId();
        $this->tokenMock['createdAt'] = $tokenFound->getCreatedAt();
        // dd($this->tokenMock, $token->toArray());
        $this->assertEquals($this->tokenMock, $token->toArray());
    }

    public function testSessionWorks()
    {
        $this->sessionUtil->set('ronaldo', 'rei da pelada');
        $ronaldo = $this->sessionUtil->get('ronaldo');
        // dd($ronaldo);
        $this->assertEquals('rei da pelada', $ronaldo);
        $this->assertNotEquals('pipoqueiro', $ronaldo);
    }
    // assert Unauthorized
    // assert no header
    // assert jwt malformed
}
