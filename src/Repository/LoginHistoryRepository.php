<?php

namespace App\Repository;

use App\Entity\LoginHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LoginHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method LoginHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoginHistory[]    findAll()
 * @method LoginHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoginHistoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LoginHistory::class);
    }

    // /**
    //  * @return LoginHistory[] Returns an array of LoginHistory objects
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
    public function findOneBySomeField($value): ?LoginHistory
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * Get three last records of login history within 30 min
     *
     * @param $ipUser
     * @return int
     * @throws \Exception
     */
    public function getRecordByIp30($ipUser)
    {
        $now = new \DateTime();
        $timezone = new \DateTimeZone('Indian/Mauritius');
        $now->setTimezone($timezone);
        $to = $now->format('Y-m-d H:i:s');
        $from = $now->modify('-30 minutes')->format('Y-m-d H:i:s');

        return $this->createQueryBuilder('l')
            ->andWhere('l.UserIp = :userIp')
            ->andWhere('l.timestamp BETWEEN :from AND :to')
            ->andWhere('l.status = :status')
            ->setParameter('status', LoginHistory::NOT_ALLOW)
            ->setParameter('userIp', $ipUser)
            ->setParameter('to', $to)
            ->setParameter('from', $from)
            ->orderBy('l.timestamp', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Find last record of block status
     *
     * @param $userIp
     * @return LoginHistory
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findLastBlockIp($userIp)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.UserIp = :userIp')
            ->andWhere('l.status = :status')
            ->setParameter('userIp', $userIp)
            ->setParameter('status', LoginHistory::BLOCK)
            ->orderBy('l.timestamp', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
