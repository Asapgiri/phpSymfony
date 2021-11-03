<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use \Doctrine\ORM\Mapping as ORM;

/**
 * Class AdUnits
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="billdata")
 */
class BillingData {
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var User|null
     * @ORM\JoinColumn(name="user", referencedColumnName="user_id", nullable=true)
     * @ORM\ManyToOne(targetEntity="User", inversedBy="billing")
     */
    private ?User $user;

    /**
     * @var ArrayCollection|null
     * @ORM\OneToMany(targetEntity="Advertisement", mappedBy="billing")
     */
    private $ads;

    /**
     * @var int
     * @ORM\Column(type="integer", length=4, nullable=false)
     */
    private int $post_code;

    /**
     * @var string
     * @ORM\Column(type="string", length=55, nullable=false)
     */
    private string $city;

    /**
     * @var string
     * @ORM\Column(type="string", length=550, nullable=false)
     */
    private string $address;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private \DateTime $created;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=13, nullable=true)
     */
    private ?string $tax_number;

    public function __toString(): string
    {
        if ($this->tax_number) return $this->post_code." ".$this->city.", ".$this->address." - adsz: ".$this->tax_number;
        else return $this->post_code." ".$this->city.", ".$this->address;
    }

    /**
     * BillingData constructor.
     * @param User|null $user
     * @param int $post_code
     * @param string $city
     * @param string $address
     * @param string|null $tax_number
     */
    public function __construct(int $post_code, string $city, string $address, ?User $user, ?string $tax_number)
    {
        $this->user = $user;
        $this->post_code = $post_code;
        $this->city = $city;
        $this->address = $address;
        $this->tax_number = $tax_number;
        $this->created = new \DateTime();
    }

    //<editor-fold desc="Getters and Setters">

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getPostCode(): int
    {
        return $this->post_code;
    }

    /**
     * @param int $post_code
     */
    public function setPostCode(int $post_code): void
    {
        $this->post_code = $post_code;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return \DateTime
     */
    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated(\DateTime $created): void
    {
        $this->created = $created;
    }

    /**
     * @return ArrayCollection|null
     */
    public function getAds(): ?ArrayCollection
    {
        return $this->ads;
    }

    /**
     * @param ArrayCollection|null $ads
     */
    public function setAds(?ArrayCollection $ads): void
    {
        $this->ads = $ads;
    }

    /**
     * @return string|null
     */
    public function getTaxNumber(): ?string
    {
        return $this->tax_number;
    }

    /**
     * @param string|null $tax_number
     */
    public function setTaxNumber(?string $tax_number): void
    {
        $this->tax_number = $tax_number;
    }
    //</editor-fold>
}