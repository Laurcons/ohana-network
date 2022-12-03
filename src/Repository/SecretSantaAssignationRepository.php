<?php

namespace App\Repository;

use App\Entity\SecretSantaAssignation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SecretSantaAssignation|null find($id, $lockMode = null, $lockVersion = null)
 * @method SecretSantaAssignation|null findOneBy(array $criteria, array $orderBy = null)
 * @method SecretSantaAssignation[]    findAll()
 * @method SecretSantaAssignation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SecretSantaAssignationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SecretSantaAssignation::class);
    }

    public function findBySender(User $user): ?SecretSantaAssignation {
        return $this->createQueryBuilder('ssa')
            ->where('ssa.sender = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function isSecretSantaStarted() {
        return intval($this->createQueryBuilder('ssa')
            ->select('count(ssa)')
            ->where('ssa.receiver IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult()) !== 0;
    }

    // /**
    //  * @return SecretSantaAssignation[] Returns an array of SecretSantaAssignation objects
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
    public function findOneBySomeField($value): ?SecretSantaAssignation
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
