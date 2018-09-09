<?php

namespace App\Repository;

use App\Entity\Track;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Track|null find($id, $lockMode = null, $lockVersion = null)
 * @method Track|null findOneBy(array $criteria, array $orderBy = null)
 * @method Track[]    findAll()
 * @method Track[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Track::class);
    }

    /**
     * @return Track[] Returns an array of Track objects
     */

    public function findOneByUserIdandEmail($userid, $campaignId, $email)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user_id = :val')
            ->andWhere('t.campaign_id = :val2')
            ->andWhere('t.sent_to = :val3')
            ->setParameter('val', $userid)
            ->setParameter('val2', $campaignId)
            ->setParameter('val3', $email)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
//            ->getOneOrNullResult()
        ;
    }
//
//    /**
//     * @return Track[] Returns an array of Track objects
//     */
//
//    public function findByUserIdYearly($value,$year)
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.user_id = :val')
//            ->andWhere("DATE_FORMAT(t.tracking_date, '%Y') = :year")
//            ->setParameter('val', $value)
//            ->setParameter('year', $year)
//            ->orderBy('t.id', 'ASC')
//            ->getQuery()
//            ->getResult()
//            ;
//    }


    /*
    public function findOneBySomeField($value): ?Track
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
