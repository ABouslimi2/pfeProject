<?php

namespace App\Repository;

use App\Entity\ServerEndpoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ServerEndpoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServerEndpoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServerEndpoint[]    findAll()
 * @method ServerEndpoint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServerEndpointRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServerEndpoint::class);
    }

    // /**
    //  * @return ServerEndpoint[] Returns an array of ServerEndpoint objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findByUrl($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.gitlabURL = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }
  
    public function findByUrlTakeFirst($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.gitlabURL = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
            ->first()
        ;
    }
    
    public function findTeamByTypeGitlab()
    {
        $result = $this->createQueryBuilder('e')
               ->select('e')
               ->andWhere('e.gitType = :val')
                ->setParameter('val', 'Gitlab')
               ->getQuery()
               ->getArrayResult();
               return $result;
    }

    public function findTeamByTypeGithub()
    {
        $result = $this->createQueryBuilder('e')
               ->select('e')
               ->andWhere('e.gitType = :val')
                ->setParameter('val', 'Github')
               ->getQuery()
               ->getArrayResult();
               return $result;
    }
   
   

}
