<?php

namespace App\Tests;

use App\Document\Token;
use PHPUnit\Framework\TestCase;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\DBAL\UniqueConstraintViolationException;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Request;
// use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class JwtTest extends KernelTestCase
{
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $dm;
    private $tokenRepository;
    private $cache;
    // private $jwtAuthenticator;
    private $tokenMock;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->dm = $kernel->getContainer()
            ->get('doctrine_mongodb')
            ->getManager();
        
        $this->tokenRepository = $this->dm->getRepository(Token::class);
        $this->cache = self::$container->get('Symfony\Contracts\Cache\CacheInterface');
        $this->jwtAuthenticator = self::$container->get('App\Security\JwtAuthenticator');
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

    public function testFindLastest()
    {
        $this->store();
        $email = $this->tokenMock['email'];
        $token = $this->tokenRepository->findLatestByEmail($email)->toArray();
        $this->assertEquals($this->tokenMock['key'], $token['key']);
    }

    public function testUJwtStart()
    {
        $response = $this->jwtAuthenticator->start(new Request);
        $content = $response->getContent();
        $statusCode = $response->getStatusCode();

        $this->assertEquals('Authentication Required', json_decode($content, 1)['message']);
        $this->assertEquals(401, $statusCode);
    }

    public function testUJwtGetUser()
    {
        $this->assertTrue(true);
    }

    public function testUJwtCheckCredentials()
    {
        $this->assertTrue(true);
    }

    public function testUJwtOnAuthenticationFailure()
    {
        $response = $this->jwtAuthenticator->onAuthenticationFailure(new Request, new AuthenticationException);
        $content = $response->getContent();
        $statusCode = $response->getStatusCode();
        // dd(json_decode($content, 1));
        $this->assertEquals('Unauthorized access.', json_decode($content, 1)['message']);
        $this->assertEquals(401, $statusCode);
    }

    public function testUJwtOnAuthenticationSuccess()
    {
        // $response = $this->jwtAuthenticator->onAuthenticationSuccess(new Request, new TokenInterface, '');
        // dd($response);
        $this->assertFalse(false);
    }

    public function testUJwtSupportsRememberMe()
    {
        $response = $this->jwtAuthenticator->supportsRememberMe();
        $this->assertFalse($response);
    }

    
    // assert Unauthorized
    // assert no header
    // assert jwt malformed
}

// fazer request sem authorization -> assert 401
// fazer request com token errado -> assert 401?
// fazer request com senha errada -> assert 401
// fazer request com senha correta -> assert 200
