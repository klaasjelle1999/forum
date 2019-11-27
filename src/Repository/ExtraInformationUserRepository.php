<?php

namespace App\Repository;

use App\Entity\ExtraInformationUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ExtraInformationUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExtraInformationUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExtraInformationUser[]    findAll()
 * @method ExtraInformationUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExtraInformationUserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ExtraInformationUser::class);
    }

    // /**
    //  * @return ExtraInformationUser[] Returns an array of ExtraInformationUser objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExtraInformationUser
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
