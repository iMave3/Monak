<?php

namespace App\Entity;

use App\Repository\CompanyInformationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyInformationRepository::class)]
class CompanyInformation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $companyName = null;

    #[ORM\Column]
    private ?int $ico = null;

    #[ORM\Column(length: 255)]
    private ?string $dic = null;

    #[ORM\OneToOne(mappedBy: 'companyInformation', cascade: ['persist', 'remove'])]
    private ?OrderSummary $orderSummary = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isValid() : bool
    {
        return $this->companyName && $this->dic;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getIco(): ?int
    {
        return $this->ico;
    }

    public function setIco(?int $ico): static
    {
        $this->ico = $ico;

        return $this;
    }

    public function getDic(): ?string
    {
        return $this->dic;
    }

    public function setDic(?string $dic): static
    {
        $this->dic = $dic;

        return $this;
    }

    public function getOrderSummary(): ?OrderSummary
    {
        return $this->orderSummary;
    }

    public function setOrderSummary(?OrderSummary $orderSummary): static
    {
        // unset the owning side of the relation if necessary
        if ($orderSummary === null && $this->orderSummary !== null) {
            $this->orderSummary->setCompanyInformation(null);
        }

        // set the owning side of the relation if necessary
        if ($orderSummary !== null && $orderSummary->getCompanyInformation() !== $this) {
            $orderSummary->setCompanyInformation($this);
        }

        $this->orderSummary = $orderSummary;

        return $this;
    }
}
