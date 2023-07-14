<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class MediaController extends AbstractController
{
   #[Route('/media', name: 'media')]
   public function index(EntityManagerInterface $entityManager = null)
   {
      $this->optimizeImage($entityManager);
      return (new Response('Hello World!'));
   }
   public function optimizeImage(EntityManagerInterface $entityManager)
   {
      if (!$entityManager) {
         return;
      }

      $path = '/var/www/html/public/Media/lion.png';
      $destinationPath = '/var/www/html/public/Media/';
      $path = $this->optimize($path, '/var/www/html/public/Media/lion3.png');
      die();
      $media = new Media();
      $media->setPath($path);
      $entityManager->persist($media);
      $entityManager->flush();
   }
   //width and height are optional parameters that default to 247 and 327 respectively 
   private function optimize($sourcePath, $destinationPath, $newWidth = 247, $newHeight = 327)
   {
      // Get the file extension
      $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);

      // Load the source image based on the file extension
      $sourceImage = null;
      switch ($extension) {
         case 'jpg':
         case 'jpeg':
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
         case 'png':
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
         case 'gif':
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
      }

      if (!$sourceImage) {
         return false;
      }

      // Get the current image dimensions
      $width = imagesx($sourceImage);
      $height = imagesy($sourceImage);

      // Create a new blank image with the desired width and height
      $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

      // Resize the image
      imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

      // Set the new image resolution (PPI)
      $newPPI = 72;
      $newDPI = $newPPI / 0.0254; // Convert PPI to DPI (dots per inch)

      // Save the resized image
      imagepng($resizedImage, $destinationPath);

      // Clean up resources
      imagedestroy($sourceImage);
      imagedestroy($resizedImage);

      return $destinationPath;
   }
}
