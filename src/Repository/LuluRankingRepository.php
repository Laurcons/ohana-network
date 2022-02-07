<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\LuluRanking;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method LuluRanking|null find($id, $lockMode = null, $lockVersion = null)
 * @method LuluRanking|null findOneBy(array $criteria, array $orderBy = null)
 * @method LuluRanking[]    findAll()
 * @method LuluRanking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LuluRankingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LuluRanking::class);
    }

    public function getLatestForUser(User $user) {
        return $this->createQueryBuilder('r')
            ->where('r.target = :target')
            ->orderBy('r.timestamp', 'desc')
            ->setMaxResults(1)
            ->setParameter('target', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return LuluRanking[] Returns an array of LuluRanking objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LuluRanking
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
