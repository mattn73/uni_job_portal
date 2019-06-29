<?php

namespace App\Repository;

use App\Entity\Application;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Application|null find($id, $lockMode = null, $lockVersion = null)
 * @method Application|null findOneBy(array $criteria, array $orderBy = null)
 * @method Application[]    findAll()
 * @method Application[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Application::class);
    }



    public function findByCompany($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.company = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findBySeeker($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.seeker = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function exist($job,$seeker){
        return $this->createQueryBuilder('a')
            ->andWhere('a.seeker = :seeker')
            ->andWhere('a.job = :job')
            ->setParameter('seeker', $seeker)
            ->setParameter('job', $job)
            ->getQuery()
            ->getResult()
            ;
    }
}
