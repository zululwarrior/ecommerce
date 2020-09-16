<?php

namespace App\Controller;

use App\Entity\BasketRow;
use App\Entity\EOrder;
use App\Entity\OrderRow;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\WebsiteService;
use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Category;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
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
use Symfony\Component\Validator\Constraints\Length;


class UserController extends Controller
{
    /**
     * @Route("/account", name="accountDashboard", methods={"GET"})
     */
    public function accountDashboard(){
        $user = $this->getUser();
        if($user){
            return $this->render('User/accountDashboard.html.twig', array('user'=>$user));
        }
        else{
            $response = $this->forward('App\Controller\ProductController::index');
            return $response;
        }
    }

    /**
     * @Route ("/account/editAccount", name="editAccount", methods={"GET","POST"})
     */

    public function editUser(Request $request, UserPasswordEncoderInterface $passwordEncoder){
        $user = $this->getUser();
        if($user){
        $ePassword = $user->getPassword();

        $form = $this->createFormBuilder($user)
            ->add('first_name', TextType::class,
                array('attr' => array('class'=>'form-control')))
            ->add('last_name', TextType::class,
                array('attr' => array('class'=>'form-control')))
            ->add('email', TextType::class,
                array('attr' => array('class' => 'form-control')))
            ->add('password', TextType::class, array('constraints' => [
                new Length([
                    'min' => 6,
                    'minMessage' => 'The password should be at least {{ limit }} characters',
                    'max' => 4096,
                ])],
                'attr' => array('class' => 'form-control'), 'data'=>'','required'=>false, 'empty_data'=>''
            ))
            ->getForm()->add('save', SubmitType::class, array(
                'label' => 'Update',
                'attr' => array('class' => 'btn btn-primary mt-3')
            ));
        $isEmpty = true;

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            if($form->get('password')->getData() !== ''){
                $isEmpty = false;
            }

            if(!$isEmpty){
                $user->setPassword($passwordEncoder->encodePassword($user, $form->get('password')->getData()));
            } else {
                $user->setPassword($ePassword);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('accountDashboard');
        }
        return $this->render('User/editAccount.html.twig', array(
            'form' => $form->createView()
        ));
        }
        else{
            $response = $this->forward('App\Controller\ProductController::index');
            return $response;
        }
    }

    /**
     * @Route ("/account/viewOrders", name="viewOrders", methods={"GET", "POST"})
     */

    public function viewOrders(){
        $user = $this->getUser();
        if($user) {
            $orders = $this->getUser()->getEOrder();

            return $this->render('User/viewOrders.html.twig', array(
                'orders' => $orders));
        }
        else{
            $response = $this->forward('App\Controller\ProductController::index');
            return $response;
        }
    }
}