<?php

namespace App\Controller;

use App\Entity\BasketRow;
use App\Entity\EOrder;
use App\Entity\Product;
use App\Entity\User;
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


class OrderController extends Controller
{
    /**
     * @Route("/purchase", name="purchase", methods={"GET", "POST"})
     */
    public function purchase(Request $request, ProductRepository $productRepository){

        $totalPrice = 0;
        $basketRows = $this->getUser()->getBasketRows();

        foreach($basketRows as $basketRow){
            $qtyPrice = $basketRow->getQuantity() * $basketRow->getProduct()->getPrice();
            $totalPrice += $qtyPrice;
        }

        $order = new EOrder;

        $form = $this->createFormBuilder($order)
            ->add('addressLine', TextType::class,
                array('attr' => array('class'=>'form-control')))
            ->add('city', TextType::class,
                array('attr' => array('class'=>'form-control')))
            ->add('postcode', TextType::class,
                array('attr' => array('class' => 'form-control')))
            ->add('Purchase', SubmitType::class, array(
                'label' => 'Purchase',
                'attr' => array('id' => 'btn btn-primary mt-3')
            ))
            ->getForm();


        $order->setTotalPrice($totalPrice);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $basketRows = $this->getUser()->getBasketRows();

            $entityManager->persist($form->getData());
            $entityManager->persist($this->getUser()->makeOrder($order));
            foreach($basketRows as $basketRow){
                $entityManager->persist($this->getUser()->removeBasketRow($basketRow));
            }
            $entityManager->flush();

            $message = "Thank you, your payment has been accepted and your order has been placed.";
            echo "<script type='text/javascript'>alert('$message');</script>";

            return $this->render('products/home.html.twig', array(
                'form' => $form->createView(), 'microphones' => $productRepository->getNewestProducts("Microphone"),
                'mice' => $productRepository->getNewestProducts("Mouse"),
                'keyboards' => $productRepository->getNewestProducts("Keyboard")));
        }

        return $this->render('User/purchase.twig', array(
            'form' => $form->createView(), 'totalPrice' => $totalPrice));
    }

}