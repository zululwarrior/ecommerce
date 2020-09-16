<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * Method({"GET"})
     */
    public function login(AuthenticationUtils $authenticationUtils, ProductRepository $productRepository): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        if (!$this->getUser()){
            return $this->render('security/login.html.twig',
                ['last_username' => $lastUsername, 'error' => $error]);
        } else {
            return $this->render('products/home.html.twig', array
            ('microphones' => $productRepository->getNewestProducts("Microphone"),
                'mice' => $productRepository->getNewestProducts("Mouse"),
                'keyboards' => $productRepository->getNewestProducts("Keyboard")));
        }
    }


}
