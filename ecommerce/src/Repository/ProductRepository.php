<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function searchProduct($text):array{

        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            SELECT * FROM product p
            WHERE p.model LIKE '%' :text '%'
            OR p.brand LIKE '%' :text '%' 
            OR p.description LIKE '%' :text '%'
            OR p.category LIKE '%' :text '%'
            OR p.name LIKE '%' :text '%'" ;

        $stmt = $conn->prepare($sql);
        $stmt->execute(['text' => $text]);

        return $stmt->fetchAll();
    }
    /**
     * @return Product[] Returns an array of Product objects
     */
    public function getNewestProducts($text){
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :text')
            ->setParameter('text', $text)
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function getCategories(){
        return $this->createQueryBuilder('p')
            ->groupBy('p.category')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function getProductViaCategory($text){
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :text')
            ->setParameter('text', $text)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function getBrandByCategory($text){
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :text')
            ->setParameter('text', $text)
            ->groupBy('p.brand')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function getProductByBrandAndCategory($category, $brand){
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :category')
            ->andWhere('p.brand = :brand')
            ->setParameter('category', $category)
            ->setParameter('brand', $brand)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
