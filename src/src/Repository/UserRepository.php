<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    private $passwordEncoder;
    public function __construct(ManagerRegistry $registry, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct($registry, User::class);
        $this->passwordEncoder = $passwordEncoder;
    }

    private function encodePassword(string &$password)
    {
        $password = $this->passwordEncoder->encodePassword(
            new User,
            $password
        );
    }

    private function mapUsersArray($users): array
    {
        return array_map(function($user){
            return $user->toArray();
        }, $users);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * List all users on database
     */
    public function listAll(){
        $users = $this->findAll();
        
        return $this->mapUsersArray($users);
    }

    /**
     * Stores user on database
     */
    public function store(array $data){
        $user = new User();

        $this->encodePassword($data['password']);

        $user
            ->setName($data['name'])
            ->setEmail($data['email'])
            ->setPassword($data['password'])
            ->setPhone($data['phone'])
        ;

        $this->_em->persist($user);
        $this->_em->flush();
        
        return $user;
    }

    /**
     * Get user on database by it's ID
     */
    public function show(int $id){
        $user = $this->find($id);
        return $user;
    }

    /**
     * Update user on database
     */
    public function update(User $user, array $data){

        if (!empty($data['name'])) $user->setName($data['name']);
        if (!empty($data['email'])) $user->setEmail($data['email']);
        if (!empty($data['phone'])) $user->setPhone($data['phone']);
        if (!empty($data['password'])) $this->encodePassword($data['password']) && $user->setPassword($data['password']);
        
        $this->_em->flush();
        
        return true;
    }

    /**
     * Delete user on database
     */
    public function delete(User $user){
        $this->_em->remove($user);
        $this->_em->flush();
        
        return true;
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /**
     * @return User | null - Returns an array of User objects
     */
    public function findByEmail($email): ?User
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.email = :val')
            ->setParameter('val', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
