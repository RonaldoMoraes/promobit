<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\User;
use Doctrine\DBAL\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginFunctionalTest extends WebTestCase
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

    // TO DO: Separate this method in a Fixture
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

    private function loginUser($client)
    {
        $request = array(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(array('email' => $this->userMock['email'], 'password' => $this->userMock['password']))
        );        

        $client->request(...$request);
    }

    // [ FUNCTIONAL TEST ]
    public function testFLogin()
    {
        $clientFirst = static::createClient();
        $clientSecond = clone($clientFirst);

        $this->registerUser($clientFirst);
        $this->loginUser($clientSecond);

        $response = $clientSecond->getResponse();
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('token', $responseData['data']);
    }

}
