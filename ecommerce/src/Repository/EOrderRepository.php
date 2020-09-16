<?php

namespace App\Repository;

use App\Entity\EOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method EOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method EOrder[]    findAll()
 * @method EOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EOrderRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EOrder::class);
    }

    // /**
    //  * @return EOrder[] Returns an array of EOrder objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EOrder
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
