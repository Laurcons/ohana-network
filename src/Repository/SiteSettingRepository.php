<?php

namespace App\Repository;

use App\Entity\SiteSetting;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method SiteSetting|null find($id, $lockMode = null, $lockVersion = null)
 * @method SiteSetting|null findOneBy(array $criteria, array $orderBy = null)
 * @method SiteSetting[]    findAll()
 * @method SiteSetting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SiteSettingRepository extends ServiceEntityRepository
{
    /** @var ManagerRegistry */
    protected $registry;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SiteSetting::class);
        $this->registry = $registry;
    }

    public function getSetting(string $name): ?SiteSetting
    {
        try {
            return $this->createQueryBuilder('s')
                ->where('s.name = :name')
                ->setParameter('name', $name)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $_) {
            return null;
        }
    }

    public function setSetting(string $name, string $value): void
    {
        try {
            $setting = $this->getSetting($name);
        } catch (NoResultException $_) {
            $setting = new SiteSetting();
            $setting->setName($name);
        }
        $setting->setValue($value);
        $this->registry->getManager()->flush();
    }

    // /**
    //  * @return SiteSetting[] Returns an array of SiteSetting objects
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
    public function findOneBySomeField($value): ?SiteSetting
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
