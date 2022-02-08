<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Tellonym;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Tellonym|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tellonym|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tellonym[]    findAll()
 * @method Tellonym[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TellonymRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tellonym::class);
    }

    public function findForUserOrderedUnhidden(User $user)
    {
        return $this->createQueryBuilder('t')
            ->where('t.destination = :user')
            ->andWhere('t.hidden = 0')
            ->setParameter('user', $user)
            ->orderBy('t.timestamp', 'desc')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Tellonym[] Returns an array of Tellonym objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Tellonym
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
