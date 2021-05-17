<?php

namespace App\Repository;

use App\Entity\MapLDS;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MapLDS|null find($id, $lockMode = null, $lockVersion = null)
 * @method MapLDS|null findOneBy(array $criteria, array $orderBy = null)
 * @method MapLDS[]    findAll()
 * @method MapLDS[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MapLDSRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MapLDS::class);
    }

    // /**
    //  * @return MapLDS[] Returns an array of MapLDS objects
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
    public function findOneBySomeField($value): ?MapLDS
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
