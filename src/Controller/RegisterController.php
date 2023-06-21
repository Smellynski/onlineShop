<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Entity\ShoppingCart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
   #[Route('/register', name: 'register')]
   public function register(Request $request, EntityManagerInterface $entityManagerInterface, UserPasswordHasherInterface $passwordHasher): Response
   {
      $requestData = $request->get('submit');
      $username = strval($request->request->get('username'));
      $email = strval($request->request->get('email'));
      $password = strval($request->request->get('password'));
      if (isset($requestData)) {
         $user = new User();
         $hashedpwd = $passwordHasher->hashPassword($user, $password);
         $user->setUsername($username);
         $user->setEmail($email);
         $user->setPassword($hashedpwd);
         $user->setShoppingCart(new ShoppingCart());
         $user->setRoles(['ROLE_USER']);
         $entityManagerInterface->persist($user);
         $entityManagerInterface->flush();
      }
      return $this->render('register/registerPage.html.twig');
   }
}
