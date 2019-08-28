<?php

namespace App\Repository;

use App\Entity\ScraperExecution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ScraperExecution|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScraperExecution|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScraperExecution[]    findAll()
 * @method ScraperExecution[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScraperExecutionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScraperExecution::class);
    }

    // /**
    //  * @return ScraperExecution[] Returns an array of ScraperExecution objects
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

    /*
    public function findOneBySomeField($value): ?ScraperExecution
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
