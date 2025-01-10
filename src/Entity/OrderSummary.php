<?php

namespace App\Entity;

use App\Repository\OrderSummaryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderSummaryRepository::class)]
class OrderSummary
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $totalPrice = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\OneToOne(inversedBy: 'orderSummary', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserInformation $userInformation = null;

    #[ORM\OneToOne(inversedBy: 'orderSummary', cascade: ['persist', 'remove'])]
    private ?CompanyInformation $companyInformation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getUserInformation(): ?UserInformation
    {
        return $this->userInformation;
    }

    public function setUserInformation(UserInformation $userInformation): static
    {
        $this->userInformation = $userInformation;

        return $this;
    }

    public function getCompanyInformation(): ?CompanyInformation
    {
        return $this->companyInformation;
    }

    public function setCompanyInformation(?CompanyInformation $companyInformation): static
    {
        $this->companyInformation = $companyInformation;

        return $this;
    }
}
