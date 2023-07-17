<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;

class OnlineShopController extends AbstractController
{
   #[Route('/', name: 'homepage')]
   public function startpage(ProductRepository $productRepository): Response
   {
      $products = $productRepository->findAll();
      $image = $this->getAllImages($products);
      return $this->render(
         'OnlineShop/startPage.html.twig',
         [
            'products' => $products,
            'image' => $image,
         ]
      );
   }

   public function getAllImages($products)
   {
      foreach ($products as $product) {
         if ($product->getImagePath() != null) {
            $image = file_get_contents($product->getImagePath());
            return $image;
         }
      }
   }

   #[Route('/getInfoForProductpage', name: 'getInfoForProductpage')]
   public function getInfoForProductpage(ProductRepository $productRepository)
   {
      return false;
   }
}
