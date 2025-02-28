<?php

namespace App\Entity;

use App\Repository\OrderSummaryRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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

    /**
     * @var Collection<int, OrderSet>
     */
    #[ORM\OneToMany(targetEntity: OrderSet::class, mappedBy: 'orderSummary', orphanRemoval: true)]
    private Collection $orderSets;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    public function __construct(float $totalPrice, UserInformation $userInformation)
    {
        $this->orderSets = new ArrayCollection();
        $this->status = 'pending';
        $this->totalPrice = $totalPrice;
        $this->setUserInformation($userInformation);
        $this->created_at = new DateTime();
    }

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

    /**
     * @return Collection<int, OrderSet>
     */
    public function getOrderSets(): Collection
    {
        return $this->orderSets;
    }

    public function addOrderSet(OrderSet $orderSet): static
    {
        if (!$this->orderSets->contains($orderSet)) {
            $this->orderSets->add($orderSet);
            $orderSet->setOrderSummary($this);
        }

        return $this;
    }

    public function removeOrderSet(OrderSet $orderSet): static
    {
        if ($this->orderSets->removeElement($orderSet)) {
            // set the owning side to null (unless already changed)
            if ($orderSet->getOrderSummary() === $this) {
                $orderSet->setOrderSummary(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
}
