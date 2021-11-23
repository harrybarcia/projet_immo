<?php

namespace App\Repository;

use App\Entity\Coords;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Coords|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coords|null findOneBy(array $criteria, array $orderBy = null)
 * @method Coords[]    findAll()
 * @method Coords[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoordsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coords::class);
    }

    // /**
    //  * @return Coords[] Returns an array of Coords objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Coords
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
