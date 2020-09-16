<?php
namespace App\Controller;

use App\Entity\BasketRow;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Category;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Length;


class AdminController extends AbstractController{

    /**
     * @Route("/admin" , name="adminHome", methods="GET")
     */
    public function adminHome(){
        $user = $this->getUser();
        return $this->render('admin/home.html.twig', array('user'=>$user));
    }

    /**
     * @Route("/admin/allusers", name="allusers", methods="GET")
     */

    public function viewUsers(UserRepository $userRepository){
        return $this->render('admin/allusers.html.twig', array
        ('users' => $userRepository->findAll()));
    }

    /**
     * @Route ("/admin/edituser/{id}", name="edituser", methods={"GET","POST"})
     */

    public function editUser(Request $request, $id, UserPasswordEncoderInterface $passwordEncoder){
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

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
                'attr' => array('class' => 'form-control'), 'empty_data'=>'','required'=>false, 'data' => ''
            ))
            ->addEventListener(FormEvents::PRE_SET_DATA,function(FormEvent $event){
                $user = $event->getData();
                $role = $event->getData()->getRoles()[0];
                $form = $event->getForm();
                $form->add('role', ChoiceType::class,[
                    'choices' => [
                        'Admin'=>'ROLE_ADMIN',
                        'User'=>'ROLE_USER'
                    ]]
                );
                $user->setRole($role);

            })
            ->getForm()->add('save', SubmitType::class, array(
                'label' => 'Update',
                'attr' => array('class' => 'btn btn-primary mt-3')
            ));
        $form->handleRequest($request);

        $isEmpty = true;

        if($form->isSubmitted() && $form->isValid()){

            if($form->get('password')->getData() !== ''){
                $isEmpty = false;
            }

            if(!$isEmpty){
                $user->setPassword($passwordEncoder->encodePassword($user, $form->get('password')->getData()));
            } else {
                $user->setPassword($ePassword);
            }

            if($user->getRole() == 'ROLE_USER'){
                $user->setRoles(['ROLE_USER']);
            } else {
                $user->setRoles(['ROLE_ADMIN','ROLE_USER']);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('allusers');
        }
        return $this->render('admin/edituser.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin/viewproducts", name="viewproducts", methods={"GET", "POST"})
     */

    public function viewProducts(ProductRepository $productRepository, Request $request){

        $form = $this->createFormBuilder(null)
            ->add('Query', TextType::class, ['required' => false])
            ->add('Search', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt'
                ]
            ])
            ->getForm();
        $form->handleRequest($request);
        $searchText = $form['Query']->getData();

        if($searchText == ""){
            return $this->render('admin/viewproducts.html.twig', array
                ('form'=>$form->createView(),'products' => $productRepository->findAll() )
            );
        } else {
            return $this->render('admin/viewproducts.html.twig', array
                ('form'=>$form->createView(),'products' => $productRepository->searchProduct($searchText))
            );
        }
    }

    /**
     * @Route("/admin/editproduct/{id}", name="editproduct", methods={"GET", "POST"})
     */

    public function editProduct(ProductRepository $productRepository, Request $request,$id){
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        $product->setImage(
            new File($this->getParameter('images').'/'.$product->getImage())
        );

        $form = $this->createFormBuilder($product)
            ->add('category', ChoiceType::class,[
                'choices' => [
                    'Keyboard'=>'Keyboard',
                    'Mouse'=>'Mouse',
                    'Microphone'=>'Microphone'
                ]])
            ->add('brand', TextType::class,
                array('attr' => array('class'=>'form-control')))
            ->add('model', TextType::class,
                array('attr' => array('class'=>'form-control')))
            ->add('description', TextType::class,
                array('attr' => array('class' => 'form-control')))
            ->add('price', MoneyType::class,
                array('attr' => array('class' => 'form-control'), 'currency' => 'GBP'))
            ->add('image', FileType::class,
                ['label' => 'Image (JPG/PNG file)', 'required' => false, 'empty_data' => $product->getImage()])
            ->add('save', SubmitType::class, array(
                'label' => 'Update',
                'attr' => array('class' => 'btn btn-primary mt-3')
            ))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $product->getImage();
            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

            try{
                $file->move(
                    $this->getParameter('images'),
                    $fileName
                );
            } catch(FileException $e){

            }

            $product->setImage($fileName);
            $product->setName($form->get('brand')->getData()." ".$form->get('model')->getData()." "
                .$form->get('category')->getData());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('viewproducts');
        }

        return $this->render('admin/editproduct.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin/viewproducts/new", name="newproduct")
     * Method({"GET", "POST"})
     */
    public function newProduct(Request $request){
        $product = new Product();

        $form = $this->createFormBuilder($product)
            ->add('category', ChoiceType::class,[
                'choices' => [
                    'Keyboard'=>'Keyboard',
                    'Mouse'=>'Mouse',
                    'Microphone'=>'Microphone'
                ]])
            ->add('brand', TextType::class,
                array('attr' => array('class'=>'form-control')))
            ->add('model', TextType::class,
                array('attr' => array('class'=>'form-control')))
            ->add('description', TextType::class,
                array('attr' => array('class' => 'form-control')))
            ->add('price', MoneyType::class,
                array('attr' => array('class' => 'form-control'), 'currency' => 'GBP'))
            ->add('image', FileType::class,
                ['label' => 'Image (JPG/PNG file)'])
            ->add('save', SubmitType::class, array(
                'label' => 'Create',
                'attr' => array('class' => 'btn btn-primary mt-3')
            ))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $product->getImage();
            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

            try{
                $file->move(
                    $this->getParameter('images'),
                    $fileName
                );
            } catch(FileException $e){

            }

            $product->setImage($fileName);
            $product->setName($form->get('brand')->getData()." ".$form->get('model')->getData()." "
                .$form->get('category')->getData());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($form->getData());
            $entityManager->flush();

            return $this->redirectToRoute('viewproducts');
        }

        return $this->render('admin/newproduct.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/viewproducts/delete/{id}", name="deleteProduct")
     * @Method({"DELETE"})
     */
    public function delete(Request $request, $id){
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($product);
        $entityManager->flush();

        $response = new Response();
        $response->send();
    }


    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }




}
