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
    protected $email;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $key;

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

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
        return $this;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setKey(string $key)
    {
        $this->key = $key;
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

    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'userId' => $this->getUserId(),
            'email' => $this->getEmail(),
            'key' => $this->getKey(),
            'createdAt' => $this->getCreatedAt(),
        );
    }

}