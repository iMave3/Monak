<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $imageURL = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $isAvailable = null;

    #[ORM\Column(nullable: true)]
    private ?int $price = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tag $tag = null;

    /**
     * @var Collection<int, OrderSet>
     */
    #[ORM\OneToMany(targetEntity: OrderSet::class, mappedBy: 'product', orphanRemoval: true)]
    private Collection $orderSets;

    public function __construct(string $name, string $imageURL, bool $isAvailable, int $price)
    {
        $this->name = $name;
        $this->imageURL = $imageURL;
        $this->isAvailable = $isAvailable;
        $this->price = $price;
        $this->orderSets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageURL(): ?string
    {
        return $this->imageURL;
    }

    public function setImageURL(string $imageURL): static
    {
        $this->imageURL = $imageURL;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setAvailable(bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getTag(): ?Tag
    {
        return $this->tag;
    }

    public function setTag(?Tag $tag): static
    {
        $this->tag = $tag;

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
            $orderSet->setProduct($this);
        }

        return $this;
    }

    public function removeOrderSet(OrderSet $orderSet): static
    {
        if ($this->orderSets->removeElement($orderSet)) {
            // set the owning side to null (unless already changed)
            if ($orderSet->getProduct() === $this) {
                $orderSet->setProduct(null);
            }
        }

        return $this;
    }
}
