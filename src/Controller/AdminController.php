<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\ShoppingCart;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;

class AdminController extends AbstractController
{
   #[Route('/admin', name: 'admin')]
   public function admin(): Response
   {
      return $this->render('admin/adminPage.html.twig');
   }

   //--------------------------PRODUCTS-----------------------------

   #[Route('/admin/products', name: 'adminProducts')]
   public function adminProducts(ProductRepository $productRepository): Response
   {
      $products = $productRepository->findAll();

      return $this->render('admin/adminProducts.html.twig', [
         'products' => $products,
      ]);
   }

   #[Route('/admin/products/addProduct', name: 'addProductToDatabase', methods: ['POST'])]
   public function addProductToDatabase(Request $request, EntityManagerInterface $entityManager)
   {
      $requestData = $request->get('submit');
      $productName = strval($request->get('productName'));
      $price = floatval($request->get('price'));
      $description = strval($request->get('description'));
      $categorys = $this->makeStringToArray(strval($request->get('category')));
      if (isset($requestData)) {
         $product = new Product();
         $product->setTitle($productName);
         $product->setPrice($price);
         $product->setDescription($description);
         $product->setCategorys($categorys);
         $entityManager->persist($product);
         $entityManager->flush();
      }
      return $this->redirectToRoute('adminProducts');
   }

   private function makeStringToArray($categorys): array
   {
      return explode(",", $categorys);
   }

   #[Route("/admin/products/deleteProduct", name: "deleteProduct", methods: ['POST'])]
   public function deleteProduct(Request $request, EntityManagerInterface $entityManager)
   {
      $selectedProducts = $request->get('selectedProducts');
      if (!empty($selectedProducts)) {
         foreach ($selectedProducts as $productId) {
            $product = $entityManager->getRepository(Product::class)->find($productId);
            if ($product) {
               $entityManager->remove($product);
            }
         }
         $entityManager->flush();
      }
      return $this->redirectToRoute('adminProducts');
   }

   //--------------------------USERS-------------------------------

   #[Route('/admin/users', name: 'adminUsers')]
   public function adminUsers(UserRepository $userRepository): Response
   {
      $users = $userRepository->findAll();

      return $this->render('admin/adminUsers.html.twig', [
         'users' => $users,
      ]);
   }

   #[Route('/admin/users/add', name: 'adminAddUsers')]
   public function adminAddUsers(Request $request, EntityManagerInterface $entityManagerInterface, UserPasswordHasherInterface $passwordHasher)
   {
      $requestData = $request->get('submit');
      $username = strval($request->get('username'));
      $email = strval($request->get('email'));
      $password = strval($request->get('password'));
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
      return $this->redirectToRoute('adminUsers');
   }

   #[Route('/admin/users/delete', name: 'adminDeleteUser')]
   public function adminDeleteUser(Request $request, EntityManagerInterface $entityManager)
   {
      $selectedUsers = $request->get('selectedUsers');
      if (!empty($selectedUsers)) {
         foreach ($selectedUsers as $user) {
            $user = $entityManager->getRepository(User::class)->find($user);
            if ($user) {
               $entityManager->remove($user);
            }
         }
         $entityManager->flush();
      }
      return $this->redirectToRoute('adminUsers');
   }
}
