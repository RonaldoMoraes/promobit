<?php

namespace App\Document;

use App\Repository\TokenRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(
 *      collection="tokens",
 *      repositoryClass=TokenRepository::class
 * )
 */
class Token
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="int")
     */
    protected $userId;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $token;

    /**
     * @MongoDB\Field(type="date")
     */
    protected $createdAt;

    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId(int $userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken(string $token)
    {
        $this->token = $token;
        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();
        return $this;
    }

}