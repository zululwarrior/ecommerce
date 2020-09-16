<?php

namespace App\Controller;

use App\Entity\BasketRow;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\BasketRowRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\WebsiteService;
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


class BasketController extends Controller
{
    /**
     * @Route("/product/{product}/add", name="addbasket", methods={"POST"})
     */
    public function addProduct(Product $product, WebsiteService $websiteService){

        $this->getUser()->addProduct($product);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return new Response($websiteService->getBasketAmount($this->getUser()->getId()));
    }

    /**
     * @Route("/basket", name="viewbasket", methods={"GET", "POST"})
     */
    public function viewBasket(){

        $products = [];
        $quantities = [];
        $basketRows = $this->getUser()->getBasketRows();

        foreach ($basketRows as $basketRow){

            $products[] = $basketRow->getProduct();
            $quantities[] = $basketRow->getQuantity();
        }

        return $this->render('User/basket.html.twig', array
        ('products' => $products, 'quantities' => $quantities));
    }

    /**
     * @Route("/basket/delete/{id}", name="deleteBasketRow")
     * @Method({"GET", "POST"})
     */
    public function delete(Request $request, $id){
        $basketRows = $this->getUser()->getBasketRows();
        $entityManager = $this->getDoctrine()->getManager();

        foreach($basketRows as $basketRow){
            if($basketRow->getProduct()->getID() == $id){
                $entityManager->persist($this->getUser()->removeBasketRow($basketRow));
            }
        }
        $entityManager->flush();

        return $this->viewBasket();
    }

}