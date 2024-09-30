<?php

namespace App\Repository;

use App\Entity\Sujets;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sujets>
 *
 * @method Sujets|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sujets|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sujets[]    findAll()
 * @method Sujets[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SujetsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sujets::class);
    }

    //    /**
    //     * @return Sujets[] Returns an array of Sujets objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Sujets
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
