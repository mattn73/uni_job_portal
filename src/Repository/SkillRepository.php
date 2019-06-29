<?php

namespace App\Repository;

use App\Entity\Skill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Skill|null find($id, $lockMode = null, $lockVersion = null)
 * @method Skill|null findOneBy(array $criteria, array $orderBy = null)
 * @method Skill[]    findAll()
 * @method Skill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SkillRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Skill::class);
    }

    // /**
    //  * @return Skill[] Returns an array of Skill objects
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


    public function findBySeeker($value): ?Skill
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.seekers = :seeker')
            ->setParameter('seeker', $value)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param string $skill
     * @return mixed
     */
    public function findSeekersBySkill(string $skill)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.name LIKE :skill')
            ->leftJoin('s.seekers', 'ss')
            ->addSelect('ss')
            ->setParameter('skill', '%'.$skill.'%')
            ->getQuery()
            ->getResult()
            ;
    }
}
