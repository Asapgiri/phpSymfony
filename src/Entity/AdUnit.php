<?php
namespace App\Entity;

use \Doctrine\ORM\Mapping as ORM;

/**
 * Class AdUnits
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="adunit")
 * @ORM\HasLifecycleCallbacks
 */
class AdUnit {
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $unit_id;

    /**
     * @var string
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $unit_name;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $unit_width;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $unit_height;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $unit_type;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $unit_price;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $unit_created;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $unit_modifyed;

    /**
     * @var User
     * @ORM\JoinColumn(name="unit_author", referencedColumnName="user_id", nullable=false)
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $unit_author;

    /**
     * AdUnit constructor.
     * @param int $unit_id
     * @param string $unit_name
     * @param int $unit_width
     * @param int $unit_height
     * @param string|null $unit_type
     * @param int $unit_price
     * @param User $unit_author
     */
    public function __construct(string $unit_name, int $unit_width, int $unit_height, string $unit_type, int $unit_price, User $unit_author)
    {
        $this->unit_name = $unit_name;
        $this->unit_width = $unit_width;
        $this->unit_height = $unit_height;
        $this->unit_type = $unit_type;
        $this->unit_price = $unit_price;
        $this->unit_author = $unit_author;
    }

    /**
     * @return int
     */
    public function getUnitId(): int
    {
        return $this->unit_id;
    }

    /**
     * @param int $unit_id
     */
    public function setUnitId(int $unit_id): void
    {
        $this->unit_id = $unit_id;
    }

    /**
     * @return string
     */
    public function getUnitName(): string
    {
        return $this->unit_name;
    }

    /**
     * @param string $unit_name
     */
    public function setUnitName(string $unit_name): void
    {
        $this->unit_name = $unit_name;
    }

    /**
     * @return int
     */
    public function getUnitWidth(): int
    {
        return $this->unit_width;
    }

    /**
     * @param int $unit_width
     */
    public function setUnitWidth(int $unit_width): void
    {
        $this->unit_width = $unit_width;
    }

    /**
     * @return int
     */
    public function getUnitHeight(): int
    {
        return $this->unit_height;
    }

    /**
     * @param int $unit_height
     */
    public function setUnitHeight(int $unit_height): void
    {
        $this->unit_height = $unit_height;
    }

    /**
     * @return string|null
     */
    public function getUnitType(): ?string
    {
        return $this->unit_type;
    }

    /**
     * @param string|null $unit_type
     */
    public function setUnitType(?string $unit_type): void
    {
        $this->unit_type = $unit_type;
    }

    /**
     * @return int
     */
    public function getUnitPrice(): int
    {
        return $this->unit_price;
    }

    /**
     * @param int $unit_price
     */
    public function setUnitPrice(int $unit_price): void
    {
        $this->unit_price = $unit_price;
    }

    /**
     * @return \DateTime
     */
    public function getUnitCreated(): \DateTime
    {
        return $this->unit_created;
    }

    /**
     * @param \DateTime $unit_created
     */
    public function setUnitCreated(\DateTime $unit_created): void
    {
        $this->unit_created = $unit_created;
    }

    /**
     * @return \DateTime
     */
    public function getUnitModifyed(): \DateTime
    {
        return $this->unit_modifyed;
    }

    /**
     * @param \DateTime $unit_modifyed
     */
    public function setUnitModifyed(\DateTime $unit_modifyed): void
    {
        $this->unit_modifyed = $unit_modifyed;
    }

    /**
     * @return User
     */
    public function getUnitAuthor(): User
    {
        return $this->unit_author;
    }

    /**
     * @param User $unit_author
     */
    public function setUnitAuthor(User $unit_author): void
    {
        $this->unit_author = $unit_author;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps() : void {
        $this->unit_modifyed = new \DateTime();
        if ($this->unit_created == null) $this->unit_created = new \DateTime();
    }

    public function __toString(): string
    {
        $name = $this->unit_name;
        $width = (string)$this->unit_width;
        $height = (string)$this->unit_height;

        $type = $this->unit_type;
        $price = (string)$this->unit_price;

        for ($i = strlen($name)-1; $i < 8; $i++) {
            $name .= "_";
        }
        for ($i = strlen($width)-1; $i < 3; $i++) {
            $width = "_".$width;
        }
        for ($i = strlen($height)-1; $i < 3; $i++) {
            $height = "_".$height;
        }


        for ($i = strlen($type)-1; $i < 2; $i++) {
            $type .= "_";
        }
        for ($i = strlen($price)-1; $i < 5; $i++) {
            $price = "_".$price;
        }

        return "// ".$name.$width."_x".$height."_".$type.$price."_Ft";
    }
}