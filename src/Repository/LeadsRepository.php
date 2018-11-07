<?php

namespace App\Repository;

use App\Entity\Leads;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Leads|null find($id, $lockMode = null, $lockVersion = null)
 * @method Leads|null findOneBy(array $criteria, array $orderBy = null)
 * @method Leads[]    findAll()
 * @method Leads[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeadsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Leads::class);
    }

    /**
     * @return Leads[] Returns an array of Leads objects
     */

    public function findByListIdnotSent($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.list_id = :val')
            ->andWhere('l.sent = 0')
            ->andWhere('l.isActive = 1')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(1) // for Cronjob max 1 leads per 20 seconds = 3 each minue = 180 per hour.
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByListIdAll($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.list_id = :val')
            ->andWhere('l.isActive = 1')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }



    public function findCountLeadsByListid($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.list_id in (:val)')
            ->andWhere('l.isActive = 1')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByEmail($value)
        {
            return $this->createQueryBuilder('l')
                ->andWhere('l.email = :val')
                ->andWhere('l.isActive = 1')
                ->setParameter('val', $value)
                ->orderBy('l.id', 'ASC')
                ->getQuery()
                ->getResult()
                ;
        }



}
