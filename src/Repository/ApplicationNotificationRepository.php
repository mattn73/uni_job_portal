<?php

namespace App\Repository;

use App\Entity\ApplicationNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ApplicationNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApplicationNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApplicationNotification[]    findAll()
 * @method ApplicationNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationNotificationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ApplicationNotification::class);
    }

    // /**
    //  * @return ApplicationNotification[] Returns an array of ApplicationNotification objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ApplicationNotification
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
