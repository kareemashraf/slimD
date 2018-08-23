<?php

namespace App\Repository;

use App\Entity\History;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method History|null find($id, $lockMode = null, $lockVersion = null)
 * @method History|null findOneBy(array $criteria, array $orderBy = null)
 * @method History[]    findAll()
 * @method History[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, History::class);
    }

    /**
     * @return History[] Returns an array of History objects
     */

    public function findOneByActive()
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.isActive = :val')
            ->setParameter('val', 1)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneById($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByUserId($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.user_id = :val')
            ->andWhere('h.order_date like :date')
            ->setParameter('val', $value)
            ->setParameter('date', date('Y-m')."%" ) //where date is this month
            ->orderBy('h.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

}
