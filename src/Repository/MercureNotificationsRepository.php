<?php

namespace App\Repository;

use App\Entity\MercureNotifications;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MercureNotifications|null find($id, $lockMode = null, $lockVersion = null)
 * @method MercureNotifications|null findOneBy(array $criteria, array $orderBy = null)
 * @method MercureNotifications[]    findAll()
 * @method MercureNotifications[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MercureNotificationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MercureNotifications::class);
    }

    // /**
    //  * @return MercureNotifications[] Returns an array of MercureNotifications objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MercureNotifications
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
