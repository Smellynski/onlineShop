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
   #[Route('/admin/products/manageProductOld', name: 'managePorudctsOld')]
   public function manageProductsOld(Request $request, EntityManagerInterface $entityManager)
   {
      return $this->render('admin/manageProducts.html.twig', [
         'products' => $entityManager->getRepository(Product::class)->findAll(),
      ]);
   }

   #[Route('/admin/products/addProduct', name: 'addProductToDatabase', methods: ['POST'])]
   public function addProductToDatabase(Request $request, EntityManagerInterface $entityManager)
   {
      $requestData = $request->get('submit');
      $productName = strval($request->get('productName'));
      $price = floatval($request->get('price'));
      $description = strval($request->get('description'));
      if (isset($requestData)) {
         $product = new Product();
         $product->setTitle($productName);
         $product->setPrice($price);
         $product->setDescription($description);
         $entityManager->persist($product);
         $entityManager->flush();
      }
      return $this->redirectToRoute('adminProducts');
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

   #[Route("/admin/products/manageProduct", name: "manageProduct", methods: ['POST'])]
   public function manageProduct(Request $request, EntityManagerInterface $entityManager)
   {
      $requestData = $request->get('submit');
      $productName = strval($request->get('productName'));
      $price = floatval($request->get('price'));
      $description = strval($request->get('description'));
      $productNameOriginal = strval($request->get('productNameOriginal'));
      $priceOriginal = floatval($request->get('priceOriginal'));
      $descriptionOriginal = strval($request->get('descriptionOriginal'));

      $products = $entityManager->getRepository(Product::class)->findAll();
      foreach ($products as $product) {
         if (
            $product->getTitle() == $productNameOriginal &&
            $priceOriginal == $product->getPrice() &&
            $descriptionOriginal == $product->getDescription()
         ) {
            if (isset($requestData)) {
               $product->setTitle($productName);
               $product->setPrice($price);
               $product->setDescription($description);
               $entityManager->persist($product);
               $entityManager->flush();
            }
         }
      }


      return $this->redirectToRoute('adminProducts');
   }

   #[Route("/admin/products/getProductData", name: "getProductData", methods: ['POST'])]
   public function getProductData(Request $request, EntityManagerInterface $entityManager)
   {
      $productId = $request->get('productID');
      $products = $entityManager->getRepository(Product::class)->findAll();
      foreach ($products as $prodcut) {
         if ($prodcut->getTitle() == $productId) {
            $product = $prodcut;
         }
      }
      if ($product) {
         $productData = [
            'title' => $product->getTitle(),
            'price' => $product->getPrice(),
            'description' => $product->getDescription(),
         ];
         return $this->json($productData);
      }
      return $this->json(['error' => 'Product not found']);
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

   #[Route('/admin/users/add', name: 'adminAddUsers', methods: ['POST'])]
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

   #[Route('/admin/users/delete', name: 'adminDeleteUser', methods: ['POST'])]
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

   #[Route('/admin/users/manage', name: 'adminManageUser')]
   public function adminManageUser(Request $request, EntityManagerInterface $entityManager)
   {
      $requestData = $request->get('submit');
      $username = $request->get('username');
      $email = $request->get('email');
      $pwd = $request->get('password');
      $originalUsername = $request->get('usernameOrignal');
      $originalEmail = $request->get('emailOrignal');

      $users = $entityManager->getRepository(User::class)->findAll();
      foreach ($users as $user) {
         if (
            $user->getUsername() == $originalUsername &&
            $user->getEmail() == $originalEmail
         ) {
            if (isset($requestData)) {
               $user->setUsername($username);
               $user->setEmail($email);
               $user->setPassword($pwd);
               $entityManager->persist($user);
               $entityManager->flush();
            }
         }
      }

      return $this->redirectToRoute('adminUsers');
   }

   #[Route('/admin/users/getUserData', name: 'getUserData', methods: ['POST'])]
   public function getUserData(Request $request, EntityManagerInterface $entityManager)
   {
      $username = $request->get('username');
      $users = $entityManager->getRepository(User::class)->findAll();
      foreach ($users as $user) {
         if ($user->getUsername() == $username) {
            $userData = [
               'username' => $user->getUsername(),
               'email' => $user->getEmail(),
            ];
            return $this->json($userData);
         }
      }
      return $this->json(['error' => 'User not found']);
   }
}
