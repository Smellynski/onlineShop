<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class OnlineShopController extends AbstractController
{
   #[Route('/', name: 'homepage')]
   public function startpage(): Response
   {
      return $this->render('OnlineShop/startPage.html.twig', [
         'controller_name' => 'OnlineShopController',
      ]);
   }
}
