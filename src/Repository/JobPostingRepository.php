<?php

namespace App\Repository;

use App\Entity\JobPosting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method JobPosting|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobPosting|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobPosting[]    findAll()
 * @method JobPosting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobPostingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, JobPosting::class);
    }

    // /**
    //  * @return JobPosting[] Returns an array of JobPosting objects
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
    public function findOneBySomeField($value): ?JobPosting
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @param $company
     * @return mixed
     */
    public function findAllJobPostingsByCompany($company)
    {
        return $this->createQueryBuilder('j')
            ->leftJoin('j.company', 'jc')
            ->addSelect('jc')
            ->andWhere('j.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllValidatedJobs()
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.ClosingDate >= :cDate')
            ->setParameter('cDate', new \DateTime(), \Doctrine\DBAL\Types\Type::DATETIME)
            ->getQuery()
            ->getResult()
            ;
    }
}
