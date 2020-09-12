<?php
namespace App\Repository;

use App\Document\Token;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Remember to map this repository in the corresponding document's repositoryClass.
 * For more information on this see the previous chapter.
 */
class TokenRepository extends ServiceDocumentRepository
{
    protected $dm;
    public function __construct(ManagerRegistry $registry, DocumentManager $dm)
    {
        parent::__construct($registry, Token::class);
        $this->dm = $dm;
    }

    /**
     * Stores token on database
     */
    public function store(array $data){
        $token = new Token();
        $token
            ->setUserId($data['userId'])
            ->setEmail($data['email'])
            ->setKey($data['key'])
            ->setCreatedAt()
        ;

        $this->dm->persist($token);
        $this->dm->flush();
        // dd($token);
        return $token;
    }

    /**
     * Stores token on database
     */
    public function findLatestByEmail(string $email){
        // $token = $this->find();
        $token = $this->dm->createQueryBuilder(Token::class)
            ->field('email')->equals($email)
            ->sort('createdAt', 'DESC')
            ->limit(1)
            ->getQuery()
            ->getSingleResult()
        ;

        return $token;
    }
}