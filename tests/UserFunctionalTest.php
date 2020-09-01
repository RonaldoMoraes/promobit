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
    
    // It will only works if we use the JSEND response pattern on our controllers
    private function getDataFrom($callback, $client, $param = null)
    {
        !$param ? $this->$callback($client) : $this->$callback($client, $param);
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);
        // Because of this:
        $data = $responseData['data'];

        return $data;
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

    private function showUser($client, $id)
    {
        $request = array(
            'GET',
            "/api/users/$id",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
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

    // [ FUNCTIONAL TEST ]
    public function testFShow()
    {
        $clientFirst = static::createClient();
        $clientSecond = clone($clientFirst);

        $userFirst = $this->getDataFrom('registerUser', $clientFirst);
        $this->assertEquals(201, $clientFirst->getResponse()->getStatusCode());
        
        $userSecond = $this->getDataFrom('showUser', $clientSecond, $userFirst['id'] ?? 0);
        $this->assertEquals(200, $clientSecond->getResponse()->getStatusCode());
        
        $this->assertEquals($userFirst, $userSecond);
    }
}
