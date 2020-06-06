<?php

namespace App\Repository;

use App\Entity\JeuLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JeuLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method JeuLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method JeuLike[]    findAll()
 * @method JeuLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JeuLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JeuLike::class);
    }

    // /**
    //  * @return JeuLike[] Returns an array of JeuLike objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?JeuLike
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}