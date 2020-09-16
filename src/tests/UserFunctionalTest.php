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
        // if(!isset($responseData['data'])) dd($responseData);
        $data = $responseData['data'];

        return $data;
    }

    // private function callRequest($client, $method, $url, $data = [], $mock = false)
    // {
    //     $data = $mock ? json_encode($this->userMock) : json_encode($data);
    //     $client->request($method, $url, [], [], ['CONTENT_TYPE' => 'application/json'], $data);
    // }
    
    private function registerUser($client, $user = null)
    {
        $request = array(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($user ?? $this->userMock)
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
            json_encode([
                'email' => $this->userMock['email'],
                'password' => $this->userMock['password']
            ])
        );        

        $client->request(...$request);
    }

    private function updateUser($client, array $params, int $wrongOne = null)
    {
        
        $loginClient = clone($client);
        $id = $wrongOne ?? $params['id'];
        $token = $this->getDataFrom('loginUser', $loginClient)['token'];
        $request = array(
            'PUT',
            "/api/users/" . $id,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_Authorization' => "Bearer $token"],
            json_encode($params['data'])
        );       
        // dd($request, $params['data']);
        // dd($request);
        $client->request(...$request);
    }

    private function deleteUser($client, int $id, int $wrongOne = null)
    {
        $loginClient = clone($client);
        $id = $wrongOne ?? $id;
        $token = $this->getDataFrom('loginUser', $loginClient)['token'];
        $request = array(
            'DELETE',
            "/api/users/$id",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_Authorization' => "Bearer $token"]
        );        

        $client->request(...$request);
    }

    private function showUser($client, int $id)
    {
        $loginClient = clone($client);
        $token = $this->getDataFrom('loginUser', $loginClient)['token'];
        $request = array(
            'GET',
            "/api/users/$id",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_Authorization' => "Bearer $token"]
        );        
        $client->request(...$request);
    }

    private function listUsers($client)
    {
        $loginClient = clone($client);
        $token = $this->getDataFrom('loginUser', $loginClient)['token'];
        $request = array(
            'GET',
            "/api/users",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_Authorization' => "Bearer $token"]
        );        
        $client->request(...$request);
    }

    // [ FUNCTIONAL TEST ]
    public function testFStore()
    {
        $clientFirst = static::createClient();
        $clientSecond = clone($clientFirst);
        $clientThird = clone($clientFirst);

        // Can create the first time
        $this->registerUser($clientFirst);
        $this->assertEquals(201, $clientFirst->getResponse()->getStatusCode());
        
        // But not with the same infos (email is unique)
        $this->registerUser($clientSecond);
        $this->assertEquals(500, $clientSecond->getResponse()->getStatusCode());

        $this->registerUser($clientThird, [
            'name' => '',
            'email' => '',
            'phone' => '',
            'password' => ''
        ]);
        $this->assertEquals(400, $clientThird->getResponse()->getStatusCode());
    }

    // [ FUNCTIONAL TEST ]
    public function testFListUsers()
    {
        $clientFirst = static::createClient();
        $clientSecond = clone($clientFirst);

        $this->getDataFrom('registerUser', $clientFirst);
        $this->getDataFrom('listUsers', $clientSecond);

        $this->assertEquals(200, $clientSecond->getResponse()->getStatusCode());
    }

    // [ FUNCTIONAL TEST ]
    public function testFShow()
    {
        $clientFirst = static::createClient();
        $clientSecond = clone($clientFirst);
        $clientThird = clone($clientFirst);

        $userFirst = $this->getDataFrom('registerUser', $clientFirst);
        $this->assertEquals(201, $clientFirst->getResponse()->getStatusCode());

        $userSecond = $this->getDataFrom('showUser', $clientSecond, $userFirst['id']);
        $this->assertEquals(200, $clientSecond->getResponse()->getStatusCode());
        
        $this->assertEquals($userFirst, $userSecond);

        $this->showUser($clientThird, 9999999999);
        $this->assertEquals(400, $clientThird->getResponse()->getStatusCode());
    }

    // [ FUNCTIONAL TEST ]
    public function testFSUpdate()
    {
        $clientFirst = static::createClient();
        $clientSecond = clone($clientFirst);
        $clientThird = clone($clientFirst);

        $userFirst = $this->getDataFrom('registerUser', $clientFirst);
        $this->assertEquals(201, $clientFirst->getResponse()->getStatusCode());

        $params = array(
            'id'    => $userFirst['id'],
            'data'  => array(
                "name"      => "Ronaldinho",
                "email"     => "ronaldinho@mail.com",
                "phone"     => "35991458401",
                "password"  => "aloalo123"
            )
        );
        $this->updateUser($clientSecond, $params);
        $aux1 = $clientSecond->getResponse();
        $this->assertEquals(200, $aux1->getStatusCode());
    }

    // [ FUNCTIONAL TEST ]
    public function testFSUpdateWrongId()
    {
        $clientFirst = static::createClient();
        $clientSecond = clone($clientFirst);

        $userFirst = $this->getDataFrom('registerUser', $clientFirst);
        $this->assertEquals(201, $clientFirst->getResponse()->getStatusCode());

        $params = array(
            'id'    => $userFirst['id'],
            'data'  => array(
                "name"      => "Ronaldinho",
                "email"     => "ronaldinho@mail.com",
                "phone"     => "35991458401",
                "password"  => "aloalo123"
            )
        );

        $this->updateUser($clientSecond, $params, 9999999);
        $this->assertEquals(400, $clientSecond->getResponse()->getStatusCode());
    }

    // [ FUNCTIONAL TEST ]
    public function testFSDelete()
    {
        $clientFirst = static::createClient();
        $clientSecond = clone($clientFirst);
        $clientThird = clone($clientFirst);

        $userFirst = $this->getDataFrom('registerUser', $clientFirst);
        $this->assertEquals(201, $clientFirst->getResponse()->getStatusCode());

        $this->deleteUser($clientSecond, $userFirst['id']);
        $this->assertEquals(200, $clientSecond->getResponse()->getStatusCode());
    }

    // [ FUNCTIONAL TEST ]
    public function testFSDeleteWrongId()
    {
        $clientFirst = static::createClient();
        $clientSecond = clone($clientFirst);

        $userFirst = $this->getDataFrom('registerUser', $clientFirst);
        $this->assertEquals(201, $clientFirst->getResponse()->getStatusCode());

        $this->deleteUser($clientSecond, 9999999999);
        $this->assertEquals(400, $clientSecond->getResponse()->getStatusCode());
    }
}
