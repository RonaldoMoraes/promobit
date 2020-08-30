<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\User;
use Doctrine\DBAL\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserFunctionalTest extends WebTestCase
{
    private $userMock;

    protected function setUp()
    {
        $this->userMock = array(
            'name'      => 'Ronaldo',
            'email'     => 'ronaldo12@mail.com',
            'phone'     => '123123123',
            'password'  => 'password123'
        );

        parent::setUp();
    }
    
    // [ FUNCTIONAL TEST ]
    public function testFStore()
    {
        $clientFirst = static::createClient();
        $clientSecond = clone($clientFirst);
        $request = array(
            'POST',
            '/users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($this->userMock)
        );        

        // Can create the first time
        $clientFirst->request(...$request);
        $this->assertEquals(201, $clientFirst->getResponse()->getStatusCode());
        
        // But not with the same infos (email is unique)
        $clientSecond->request(...$request);
        $this->assertEquals(500, $clientSecond->getResponse()->getStatusCode());
    }

}
