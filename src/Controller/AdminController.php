<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProductRepository;

class AdminController extends AbstractController
{
   #[Route('/admin', name: 'admin')]
   public function admin(): Response
   {
      return $this->render('admin/adminPage.html.twig');
   }

   #[Route('/admin/products', name: 'adminProducts')]
   public function adminProducts(ProductRepository $productRepository): Response
   {
      $products = $productRepository->findAll();

      return $this->render('admin/adminProducts.html.twig', [
         'products' => $products,
      ]);
   }

   #[Route('/admin/users', name: 'adminUsers')]
   public function adminUsers(): Response
   {
      return $this->render('admin/adminUsers.html.twig');
   }

   #[Route('/admin/products/addProduct', name: 'addProductToDatabase', methods: ['POST'])]
   public function addProductToDatabase(ProductRepository $productRepository, Request $request, EntityManagerInterface $entityManager)
   {
      $requestData = $request->get('submit');
      $productName = strval($request->request->get('productName'));
      $price = floatval($request->request->get('price'));
      $description = strval($request->request->get('description'));
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

   #[Route("/admin/products/delete", name: "deleteProduct")]
   public function deleteProduct(Request $request, EntityManagerInterface $entityManager)
   {
      $selectedProducts = $request->request->get('selectedProducts');

      if (!empty($selectedProducts)) {
         foreach ($selectedProducts as $productId) {
            $product = $entityManager->getRepository(Product::class)->find($productId);
            if ($product) {
               $entityManager->remove($product);
            }
         }

         $entityManager->flush();
      }

      // Redirect to the admin products page or render a success message
      // You can adjust this based on your application's requirements
      return $this->redirectToRoute('adminProducts');
   }
}
