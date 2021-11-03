<?php
namespace App\DTO;

use App\Entity\BillingData;
use App\Entity\User;

class AdvertisementDto {

    // ads database stuff...
    /**
     * @var string
     */
    private $ad_aname = "";

    /**
     * @var User|null
     */
    private $ad_author;

    /**
     * @var string
     */
    private $ad_email = "";

    /** @var string|null */
    private $telephone;

    /**
     * @var string
     */
    private $ad_message = "";

    /**
     * @var string
     */
    private $ad_type = "";

    /**
     * @var mixed|null
     */
    private $ad_zip;

    /** @var string  */
    private string $ad_token;

    // billdata database stuff...

    /** @var int|null  */
    private $postcode;

    /** @var string|null  */
    private $city;

    /** @var string|null  */
    private $address;

    /** @var string|null  */
    private $tax_number;

    /** @var bool|null  */
    private $need_billdata;

    /** @var BillingData|null  */
    private $billing;


    /**
     * AdvertisementDto constructor.
     * @param User|null $ad_author
     */
    public function __construct(?User $ad_author = null, string $ad_token)
    {
        $this->ad_author = $ad_author;
        if ($ad_author) {
            $this->ad_email = $ad_author->getEmail();
            $this->telephone = $ad_author->getTelephone();
            if ($ad_author->getLastName() && $ad_author->getFirstName()) $this->ad_aname = $ad_author->getLastName()." ".$ad_author->getFirstName();
            else if ($ad_author->getLastName()) $this->ad_aname = $ad_author->getLastName();
            else if ($ad_author->getFirstName()) $this->ad_aname = $ad_author->getFirstName();
        }

        $this->ad_token = $ad_token;
    }

    //<editor-fold desc="Getters and Setters">
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
     * @return mixed|null
     */
    public function getAdZip()
    {
        return $this->ad_zip;
    }

    /**
     * @param mixed|null $ad_zip
     */
    public function setAdZip($ad_zip): void
    {
        $this->ad_zip = $ad_zip;
    }

    /**
     * @return string
     */
    public function getAdAname(): string
    {
        return $this->ad_aname;
    }

    /**
     * @param string $ad_aname
     */
    public function setAdAname(string $ad_aname): void
    {
        $this->ad_aname = $ad_aname;
    }

    /**
     * @return string
     */
    public function getAdToken(): string
    {
        return $this->ad_token;
    }

    /**
     * @param string $ad_token
     */
    public function setAdToken(string $ad_token): void
    {
        $this->ad_token = $ad_token;
    }

    /**
     * @return int|null
     */
    public function getPostcode(): ?int
    {
        return $this->postcode;
    }

    /**
     * @param int|null $postcode
     */
    public function setPostcode(?int $postcode): void
    {
        $this->postcode = $postcode;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     */
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     */
    public function setAddress(?string $address): void
    {
        $this->address = $address;
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

    /**
     * @return bool|null
     */
    public function isNeedBilldata(): ?bool
    {
        return $this->need_billdata;
    }

    /**
     * @param bool|null $need_billdata
     */
    public function setNeedBilldata(?bool $need_billdata): void
    {
        $this->need_billdata = $need_billdata;
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
     * @return string|null
     */
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    /**
     * @param string|null $telephone
     */
    public function setTelephone(?string $telephone): void
    {
        $this->telephone = $telephone;
    }
    //</editor-fold>
}