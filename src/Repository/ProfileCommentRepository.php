<?php

namespace App\Repository;

use App\Entity\ProfileComment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProfileComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProfileComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProfileComment[]    findAll()
 * @method ProfileComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfileCommentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProfileComment::class);
    }

    public function findAllProfileCommentsForUser(User $user)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.user', 'user')
            ->where('user.id ='. $user->getId())
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return ProfileComment[] Returns an array of ProfileComment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProfileComment
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
