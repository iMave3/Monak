<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'tag', orphanRemoval: true)]
    private Collection $products;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'childrenTags')]
    private ?self $parentTag = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parentTag', cascade:['remove'])]
    private Collection $childrenTags;

    #[ORM\Column(length: 255)]
    private ?string $imageURL = null;

    public function __construct(?string $name = null, ?string $imageURL = null, ?string $description = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->products = new ArrayCollection();
        $this->childrenTags = new ArrayCollection();
        $this->imageURL = $imageURL;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setTag($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getTag() === $this) {
                $product->setTag(null);
            }
        }

        return $this;
    }

    public function getParentTag(): ?self
    {
        return $this->parentTag;
    }

    public function setParentTag(?self $parentTag): static
    {
        $this->parentTag = $parentTag;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildrenTags(): Collection
    {
        return $this->childrenTags;
    }

    public function addChildrenTag(self $childrenTag): static
    {
        if (!$this->childrenTags->contains($childrenTag)) {
            $this->childrenTags->add($childrenTag);
            $childrenTag->setParentTag($this);
        }

        return $this;
    }

    public function removeChildrenTag(self $childrenTag): static
    {
        if ($this->childrenTags->removeElement($childrenTag)) {
            // set the owning side to null (unless already changed)
            if ($childrenTag->getParentTag() === $this) {
                $childrenTag->setParentTag(null);
            }
        }

        return $this;
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
}
