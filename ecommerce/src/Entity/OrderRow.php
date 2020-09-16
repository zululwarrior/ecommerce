<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderRowRepository")
 */
class OrderRow
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EOrder", inversedBy="orderRows")
     * @ORM\JoinColumn(nullable=false)
     */
    private $eOrder;


    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $quantityPrice;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $productModel;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $productBrand;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $productDescription;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $productCategory;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $productImage;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEOrder(): ?EOrder
    {
        return $this->eOrder;
    }

    public function setEOrder(?EOrder $eOrder): self
    {
        $this->eOrder = $eOrder;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getQuantityPrice()
    {
        return $this->quantityPrice;
    }

    public function setQuantityPrice($quantityPrice): self
    {
        $this->quantityPrice = $quantityPrice;
        return $this;
    }

    public function getProductModel(): ?string
    {
        return $this->productModel;
    }

    public function setProductModel(string $productModel): self
    {
        $this->productModel = $productModel;

        return $this;
    }

    public function getProductBrand(): ?string
    {
        return $this->productBrand;
    }

    public function setProductBrand(string $productBrand): self
    {
        $this->productBrand = $productBrand;

        return $this;
    }

    public function getProductDescription(): ?string
    {
        return $this->productDescription;
    }

    public function setProductDescription(string $productDescription): self
    {
        $this->productDescription = $productDescription;

        return $this;
    }

    public function getProductCategory(): ?string
    {
        return $this->productCategory;
    }

    public function setProductCategory(string $productCategory): self
    {
        $this->productCategory = $productCategory;

        return $this;
    }

    public function getProductImage(): ?string
    {
        return $this->productImage;
    }

    public function setProductImage(string $productImage): self
    {
        $this->productImage = $productImage;

        return $this;
    }
}
