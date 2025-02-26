<?php

namespace App\Entity;

use App\Repository\OrderSetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderSetRepository::class)]
class OrderSet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'orderSets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'orderSets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?OrderSummary $orderSummary = null;

    #[ORM\Column]
    private ?int $pricePerPiece = null;

    public function __construct(int $quantity, int $pricePerPiece)
    {
        $this->quantity = $quantity;
        $this->pricePerPiece = $pricePerPiece;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getOrderSummary(): ?OrderSummary
    {
        return $this->orderSummary;
    }

    public function setOrderSummary(?OrderSummary $orderSummary): static
    {
        $this->orderSummary = $orderSummary;

        return $this;
    }

    public function getPricePerPiece(): ?int
    {
        return $this->pricePerPiece;
    }

    public function setPricePerPiece(int $pricePerPiece): static
    {
        $this->pricePerPiece = $pricePerPiece;

        return $this;
    }
}
