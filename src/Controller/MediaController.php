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
   public function optimizeImage(EntityManagerInterface $entityManager, $originalName, $productName)
   {
      if (!$entityManager) {
         return;
      }

      $path = '/var/www/html/public/Media/temp/' . $originalName;
      $publicPath = '/Media/optimized/' . $productName . '.png';
      $destinationPath = '/var/www/html/public' . $publicPath;
      $this->optimize($path, $destinationPath);

      $media = new Media();
      $media->setPath($publicPath);
      $media->setName($productName);
      $entityManager->persist($media);
      $entityManager->flush();
      return $destinationPath;
   }
   //width and height are optional parameters that default to 247 and 327 respectively 
   private function optimize($sourcePath, $destinationPath, $newWidth = 247, $newHeight = 327)
   {
      // Get the file extension
      $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);

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

      $width = imagesx($sourceImage);
      $height = imagesy($sourceImage);

      // Create a new blank image with the desired width and height
      $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

      // Resize the image
      imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

      $newPPI = 72;
      $newDPI = $newPPI / 0.0254; // Convert PPI to DPI (dots per inch)

      imagepng($resizedImage, $destinationPath);

      imagedestroy($sourceImage);
      imagedestroy($resizedImage);
      unlink($sourcePath);
   }
}
