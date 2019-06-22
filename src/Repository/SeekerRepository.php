<?php

namespace App\Repository;

use App\Entity\Seeker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Seeker|null find($id, $lockMode = null, $lockVersion = null)
 * @method Seeker|null findOneBy(array $criteria, array $orderBy = null)
 * @method Seeker[]    findAll()
 * @method Seeker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeekerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Seeker::class);
    }

    // /**
    //  * @return Seeker[] Returns an array of Seeker objects
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
    public function findOneBySomeField($value): ?Seeker
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * find all users
     *
     * @return mixed
     */
    public function findContactPerson()
    {
        return $this->createQueryBuilder('s')
            ->getQuery()
            ->getResult()
            ;
    }
}
