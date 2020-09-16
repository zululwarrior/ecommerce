<?php

namespace App\Repository;

use App\Entity\OrderRow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method OrderRow|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderRow|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderRow[]    findAll()
 * @method OrderRow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRowRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OrderRow::class);
    }

    // /**
    //  * @return OrderRow[] Returns an array of OrderRow objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrderRow
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
