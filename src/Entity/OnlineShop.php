<?php

namespace App\Entity;

use App\Repository\OnlineShopRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OnlineShopRepository::class)]
class OnlineShop
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $products = [];

    #[ORM\Column(type: Types::ARRAY)]
    private array $users = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function setProducts(array $products): static
    {
        $this->products = $products;

        return $this;
    }

    public function getUsers(): array
    {
        return $this->users;
    }

    public function setUsers(array $users): static
    {
        $this->users = $users;

        return $this;
    }
}
