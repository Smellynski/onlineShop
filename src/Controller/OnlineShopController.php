<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping\Id;

class OnlineShopController extends AbstractController
{
   #[Route('/', name: 'homepage')]
   public function startpage(ProductRepository $productRepository): Response
   {
      $products = $productRepository->findAll();
      return $this->render(
         'OnlineShop/startPage.html.twig',
         [
            'products' => $products,
         ]
      );
   }

   #[Route('/productpage/{id}', name: 'productpage')]
   public function productpage(ProductRepository $productRepository, $id): Response
   {
      $id = intval($id);
      $product = $productRepository->findOneBy(['id' => $id]);
      return $this->render(
         'OnlineShop/productPage.html.twig',
         [
            'product' => $product,
         ]
      );
   }

   #[Route('/getInfoForProductpage', name: 'getInfoForProductpage')]
   public function getInfoForProductpage(ProductRepository $productRepository)
   {
      return false;
   }
}
