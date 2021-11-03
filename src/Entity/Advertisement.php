<?php
namespace App\Entity;

use App\Doctrine\AdIdGenerator;
use App\DTO\AdvertisementDto;
use \Doctrine\ORM\Mapping as ORM;

/**
 * Class Advertisement
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="advertisement")
 */
class Advertisement {
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $ad_id;

    /**
     * @var string
     * @ORM\Column(type="string", length=12)
     */
    private $public_id;

    /**
     * @var string
     * @ORM\Column(type="string", length=125, nullable=false)
     */
    private $ad_name;

    /**
     * @var User|null
     * @ORM\JoinColumn(name="ad_author", referencedColumnName="user_id", nullable=true)
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $ad_author;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $ad_telephone;

    /**
     * @var string
     * @ORM\Column(type="string", length=254, nullable=false)
     */
    private $ad_email;

    /**
     * @var string
     * @ORM\Column(type="string", length=10000, nullable=false)
     */
    private $ad_message;

    /**
     * @var string
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $ad_type = "";

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $ad_datetime;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ad_watched;

    /**
     * @var BillingData|null
     * @ORM\JoinColumn(name="billing", referencedColumnName="id", nullable=true)
     * @ORM\ManyToOne(targetEntity="BillingData", inversedBy="ads")
     */
    private ?BillingData $billing;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=155, nullable=true)
     */
    private $ad_zip;

    /** @var string|null */
    private $ad_files;

    /**
     * Advertisement constructor.
     * @param AdvertisementDto $dto
     */
    public function __construct(AdvertisementDto $dto)
    {
        $this->ad_name = $dto->getAdAname();
        $this->ad_author = $dto->getAdAuthor();
        $this->ad_email = $dto->getAdEmail();
        $this->ad_telephone = $dto->getTelephone();
        $this->ad_message = $dto->getAdMessage();
        $this->ad_type = $dto->getAdType();
        $this->ad_zip = $dto->getAdZip();
        $this->ad_watched = false;
        $this->ad_datetime = new \DateTime();

        $this->billing = $dto->getBilling();
        if (!$this->billing || $dto->isNeedBilldata()) $this->billing = new BillingData($dto->getPostcode(), $dto->getCity(), $dto->getAddress(), $dto->getAdAuthor(), $dto->getTaxNumber());
    }

    //<editor-fold desc="Getters and Setters">
    /**
     * @param string|null $ad_zip
     */
    public function setAdZip(?string $ad_zip): void
    {
        $this->ad_zip = $ad_zip;
    }

    /**
     * @return bool|null
     */
    public function isAdWatched(): ?bool
    {
        return $this->ad_watched;
    }

    /**
     * @param bool $ad_watched
     */
    public function setAdWatched(bool $ad_watched): void
    {
        $this->ad_watched = $ad_watched;
    }

    /**
     * @return string|null
     */
    public function getAdZip(): ?string
    {
        return $this->ad_zip;
    }

    /**
     * @return string
     */
    public function getAdName(): string
    {
        return $this->ad_name;
    }

    /**
     * @param string $ad_name
     */
    public function setAdName(string $ad_name): void
    {
        $this->ad_name = $ad_name;
    }

    /**
     * @return int
     */
    public function getAdId(): int
    {
        return $this->ad_id;
    }

    /**
     * @param int $ad_id
     */
    public function setAdId(int $ad_id): void
    {
        $this->ad_id = $ad_id;
    }

    /**
     * @return User|null
     */
    public function getAdAuthor(): ?User
    {
        return $this->ad_author;
    }

    /**
     * @param User|null $ad_author
     */
    public function setAdAuthor(?User $ad_author): void
    {
        $this->ad_author = $ad_author;
    }

    /**
     * @return string
     */
    public function getAdEmail(): string
    {
        return $this->ad_email;
    }

    /**
     * @param string $ad_email
     */
    public function setAdEmail(string $ad_email): void
    {
        $this->ad_email = $ad_email;
    }

    /**
     * @return string
     */
    public function getAdMessage(): string
    {
        return $this->ad_message;
    }

    /**
     * @param string $ad_message
     */
    public function setAdMessage(string $ad_message): void
    {
        $this->ad_message = $ad_message;
    }

    /**
     * @return string
     */
    public function getAdType(): string
    {
        return $this->ad_type;
    }

    /**
     * @param string $ad_type
     */
    public function setAdType(string $ad_type): void
    {
        $this->ad_type = $ad_type;
    }

    /**
     * @return \DateTime
     */
    public function getAdDatetime(): \DateTime
    {
        return $this->ad_datetime;
    }

    /**
     * @param \DateTime $ad_datetime
     */
    public function setAdDatetime(\DateTime $ad_datetime): void
    {
        $this->ad_datetime = $ad_datetime;
    }

    /**
     * @return string|null
     */
    public function getAdFiles(): ?string
    {
        return $this->ad_files;
    }

    /**
     * @param string|null $ad_files
     */
    public function setAdFiles(?string $ad_files): void
    {
        $this->ad_files = $ad_files;
    }

    /**
     * @return BillingData|null
     */
    public function getBilling(): ?BillingData
    {
        return $this->billing;
    }

    /**
     * @param BillingData|null $billing
     */
    public function setBilling(?BillingData $billing): void
    {
        $this->billing = $billing;
    }

    /**
     * @return string
     */
    public function getPublicId(): string
    {
        return $this->public_id;
    }

    /**
     * @param string $public_id
     */
    public function setPublicId(string $public_id): void
    {
        $this->public_id = $public_id;
    }

    /**
     * @return string|null
     */
    public function getAdTelephone(): ?string
    {
        return $this->ad_telephone;
    }

    /**
     * @param string|null $ad_telephone
     */
    public function setAdTelephone(?string $ad_telephone): void
    {
        if (strlen($ad_telephone) > 15) $ad_telephone = substr($ad_telephone, 0, 15);
        $this->ad_telephone = $ad_telephone;
    }
    //</editor-fold>
}