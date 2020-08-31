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
            'email'     => 'ronaldo@mail.com',
            'phone'     => '123123123',
            'password'  => 'password123'
        );

        parent::setUp();
    }
    
    private function registerUser($client)
    {
        $request = array(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($this->userMock)
        );        

        $client->request(...$request);
    }

    // [ FUNCTIONAL TEST ]
    public function testFStore()
    {
        $clientFirst = static::createClient();
        $clientSecond = clone($clientFirst);

        // Can create the first time
        $this->registerUser($clientFirst);
        $this->assertEquals(201, $clientFirst->getResponse()->getStatusCode());
        
        // But not with the same infos (email is unique)
        $this->registerUser($clientSecond);
        $this->assertEquals(500, $clientSecond->getResponse()->getStatusCode());
    }

}
