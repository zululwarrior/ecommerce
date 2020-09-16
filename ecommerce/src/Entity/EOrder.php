<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EOrderRepository")
 */
class EOrder
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $totalPrice;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="eOrder")
     */
    private $customer;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderRow", mappedBy="eOrder", orphanRemoval=true, cascade={"persist"})
     */
    private $orderRows;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $addressLine;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $postcode;

    public function __construct()
    {
        $this->orderRows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    public function setTotalPrice($totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Collection|OrderRow[]
     */
    public function getOrderRows(): Collection
    {
        return $this->orderRows;
    }

    public function addOrderRow(OrderRow $orderRow): self
    {
        if (!$this->orderRows->contains($orderRow)) {
            $this->orderRows[] = $orderRow;
            $orderRow->setEOrder($this);
        }

        return $this;
    }

    public function removeOrderRow(OrderRow $orderRow): self
    {
        if ($this->orderRows->contains($orderRow)) {
            $this->orderRows->removeElement($orderRow);
            // set the owning side to null (unless already changed)
            if ($orderRow->getEOrder() === $this) {
                $orderRow->setEOrder(null);
            }
        }

        return $this;
    }

    public function getAddressLine(): ?string
    {
        return $this->addressLine;
    }

    public function setAddressLine(string $addressLine): self
    {
        $this->addressLine = $addressLine;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }
}
