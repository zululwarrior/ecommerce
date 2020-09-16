<?php

namespace App\Controller;


use App\Entity\Basket;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Category;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Repository\ArticleRepository;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class ProductController extends Controller
{
    /**
     * @Route("/product/{product}", name="showproduct", methods="GET")
     */
    public function displayProduct(Product $product){
        return $this->render('products/view.html.twig', array
        ('product' => $product));
    }

    /**
     * @Route("/" , name="producthome", methods="GET")
     */
    public function index(ProductRepository $productRepository){
        return $this->render('products/home.html.twig', array
        ('microphones' => $productRepository->getNewestProducts("Microphone"),
            'mice' => $productRepository->getNewestProducts("Mouse"),
            'keyboards' => $productRepository->getNewestProducts("Keyboard")));
    }

    /**
     * @Route("/view/{category}/all" , name="showAllViaCategory", methods="GET")
     */

    public function showAllViaCategory(ProductRepository $productRepository, Product $product){

        return $this->render('products/viewAllFromCategory.html.twig',
            array('products' => $productRepository->getProductViaCategory($product->getCategory()),
                'category' => $product->getCategory()));
    }

    /**
     * @Route("/view/{category}/{brand}" , name="showProductsViaBrand", methods="GET")
     */
    public function showProductsByBrand(ProductRepository $productRepository, Product $product){

        return $this->render('products/viewByCategory.html.twig',
            array('products' => $productRepository->getProductByBrandAndCategory($product->getCategory(),$product->getBrand()),
                'brand' => $product->getBrand(),
                'category' => $product->getCategory()));
    }

}