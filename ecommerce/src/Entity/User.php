<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $lastName;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BasketRow", mappedBy="customer", cascade={"persist"}, orphanRemoval=true)
     */
    private $basketRows;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EOrder", mappedBy="customer")
     */
    private $eOrder;

    public function __construct()
    {
        $this->eOrder = new ArrayCollection();
        $this->basketRows = new ArrayCollection();
    }

    private $role;

    public function getRole(){
        return $this->role;
    }

    public function setRole(string $role){
        $this->role = $role;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }


    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }


    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function addProduct(Product $product)
    {
        $basketRows = $this->getBasketRows();
        $existingRow = null;

        foreach($basketRows as $basketRow)
        {
            if ($basketRow->getProduct()->getId() === $product->getId())
            {
                $existingRow = $basketRow;
                break;
            }
        }

        if ($existingRow != null)
        {
                $existingRow->setQuantity($existingRow->getQuantity() + 1);

        } else {
           $basketRow = new BasketRow();
           $basketRow->setQuantity(1);
           $basketRow->setProduct($product);
           $basketRow->setCustomer($this);

           $this->addBasketRow($basketRow);
        }

        return $this;
    }

    /**
     * @return Collection|BasketRow[]
     */
    public function getBasketRows(): Collection
    {
        return $this->basketRows;
    }

    public function addBasketRow(BasketRow $basketRow): self
    {
        if (!$this->basketRows->contains($basketRow)) {
            $this->basketRows[] = $basketRow;
            $basketRow->setCustomer($this);
        }

        return $this;
    }

    public function removeBasketRow(BasketRow $basketRow): self
    {
        if ($this->basketRows->contains($basketRow)) {
            $this->basketRows->removeElement($basketRow);
            // set the owning side to null (unless already changed)
            if ($basketRow->getCustomer() === $this) {
                $basketRow->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|eOrder[]
     */
    public function getEOrder(): Collection
    {
        return $this->eOrder;
    }

    public function addEOrder(EOrder $eOrder): self
    {
        if (!$this->eOrder->contains($eOrder)) {
            $this->eOrder[] = $eOrder;
            $eOrder->setCustomer($this);
        }

        return $this;
    }

    public function removeEOrder(EOrder $eOrder): self
    {
        if ($this->eOrder->contains($eOrder)) {
            $this->eOrder->removeElement($eOrder);
            // set the owning side to null (unless already changed)
            if ($eOrder->getCustomer() === $this) {
                $eOrder->setCustomer(null);
            }
        }

        return $this;
    }

    public function makeOrder(EOrder $eOrder): self
    {
        if (!$this->eOrder->contains($eOrder)) {
            $this->eOrder[] = $eOrder;
            $eOrder->setCustomer($this);

            $basketRows = $this->getBasketRows();

            foreach($basketRows as $basketRow)
            {
                $orderRow = new OrderRow();

                $orderRow->setProductModel($basketRow->getProduct()->getModel());
                $orderRow->setProductBrand($basketRow->getProduct()->getBrand());
                $orderRow->setProductDescription($basketRow->getProduct()->getDescription());
                $orderRow->setProductCategory($basketRow->getProduct()->getCategory());
                $orderRow->setProductImage($basketRow->getProduct()->getImage());
                $orderRow->setPrice($basketRow->getProduct()->getPrice());
                $orderRow->setQuantity($basketRow->getQuantity());
                $orderRow->setQuantityPrice($basketRow->getQuantity() * $basketRow->getProduct()->getPrice());
                $orderRow->setEOrder($eOrder);

                $eOrder->addOrderRow($orderRow);
            }
        }
        return $this;

    }


}
