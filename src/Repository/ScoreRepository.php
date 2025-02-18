<?php

namespace App\Repository;

use App\Entity\Score;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Score>
 *
 * @method Score|null find($id, $lockMode = null, $lockVersion = null)
 * @method Score|null findOneBy(array $criteria, array $orderBy = null)
 * @method Score[]    findAll()
 * @method Score[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Score::class);
    }


    // src/Repository/ScoreRepository.php

public function findByStudentAndModuleOrderedByDate($student, $module)
{
    return $this->createQueryBuilder('s')
        ->where('s.student = :student')
        ->andWhere('s.sujet = :module')
        ->setParameter('student', $student)
        ->setParameter('module', $module)
        ->orderBy('s.date', 'ASC')  // Order by date in ascending order
        ->getQuery()
        ->getResult();
}


// src/Repository/ScoreRepository.php

public function findScoresByStudent($studentId)
{
    return $this->createQueryBuilder('s')
        ->where('s.student = :studentId')
        ->setParameter('studentId', $studentId)
        ->getQuery()
        ->getResult();
}

    //    /**
    //     * @return Score[] Returns an array of Score objects
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

    //    public function findOneBySomeField($value): ?Score
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
