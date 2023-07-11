<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $categorys = [];

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $productImage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAllProduct(): array
    {
        $product = [
            "title" => $this->title,
            "price" => $this->price,
            "description" => $this->description,
        ];
        return $product;
    }

    public function getCategorys(): array
    {
        return $this->categorys;
    }

    public function setCategorys(array $category): static
    {
        $this->categorys = $category;

        return $this;
    }

    public function getProductImage()
    {
        return $this->productImage;
    }

    public function setProductImage($productImage): static
    {
        $this->productImage = $productImage;

        return $this;
    }
}
