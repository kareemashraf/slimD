<?php

namespace App\Repository;

use App\Entity\Emaillist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Emaillist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Emaillist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Emaillist[]    findAll()
 * @method Emaillist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmaillistRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Emaillist::class);
    }

    /**
     * @return Emaillist[] Returns an array of Emaillist objects
     */

    public function findByUserId($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.user_id = :val')
            ->andWhere('e.isActive = 1')
            ->setParameter('val', $value)
            ->orderBy('e.data_added', 'DESC')
//            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }



    public function findOneById($value): ?Emaillist
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
