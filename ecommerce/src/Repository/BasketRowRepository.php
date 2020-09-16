<?php

namespace App\Repository;

use App\Entity\BasketRow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BasketRow|null find($id, $lockMode = null, $lockVersion = null)
 * @method BasketRow|null findOneBy(array $criteria, array $orderBy = null)
 * @method BasketRow[]    findAll()
 * @method BasketRow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BasketRowRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BasketRow::class);
    }

    public function getBasketAmount($text){

        return $this->createQueryBuilder('b')
            ->andWhere('b.customer = :text')
            ->setParameter('text', $text)
            ->select('SUM(b.quantity)')
            ->getQuery()
            ->getSingleScalarResult();

    }
    /**
     * @return BasketRow Returns an array of Product objects
     */
    public function findByProductAndCustomer($text, $text1){

        return $this->createQueryBuilder('br')
            ->andWhere('br.product = :text')
            ->andWhere('br.customer = :text1')
            ->setParameter('text', $text)
            ->setParameter('text1', $text1)
            ->getQuery()
            ->getResult();
    }
    // /**
    //  * @return Basket[] Returns an array of Basket objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Basket
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
