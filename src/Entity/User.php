<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use \Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User implements UserInterface {
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $userId;

    /**
     * @var string
     * @ORM\Column(type="string", length=55, nullable=false)
     */
    private $userName;

    /**
     * @var string
     * @ORM\Column(type="string", length=254, nullable=false)
     */
    private $email;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $email_visibile = false;

    /**
     * @var string
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $userPass;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $fname;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $lname;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $reg_date;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $last_online;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=5000, nullable=true)
     */
    private $description;

    /**
     * @var string[]
     * @ORM\Column(type="json", nullable=false)
     */
    private $roles = "ROLE_USER";

    /**
     * @var ArrayCollection|null
     * @ORM\OneToMany(targetEntity="Publication", mappedBy="pub_author")
     */
    private $u_pubs;

    /**
     * @var ArrayCollection|null
     * @ORM\OneToMany(targetEntity="Forum", mappedBy="f_author")
     */
    private $u_forums;

    /**
     * @var PersistentCollection|null
     * @ORM\OneToMany(targetEntity="BillingData", mappedBy="user")
     */
    private $billing;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $telephone;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $u_avatar;

    /**
     * @var boolean|null
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $subscribed = false;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $hide_subpanel = false;



    //<editor-fold desc="Getters and Setters">
    /**
     * @return string|null
     */
    public function getUAvatar()
    {
        return $this->u_avatar;
    }

    /**
     * @return string|null
     */
    public function getUAvatarReturnFullPath()
    {
        if ($this->u_avatar) return "/users/images/".$this->u_avatar;
        else return "/users/images/no-avatar.png";
    }

    /**
     * @param ?string $u_avatar
     */
    public function setUAvatar(?string $u_avatar): void
    {
        if ($this->getUAvatar()) unlink('users/images/'.$this->getUAvatar());
        $this->u_avatar = $u_avatar;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getPassword()
    {
        return $this->userPass;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     */
    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    public function __toString() {
        return $this->getUsername();
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return bool|null
     */
    public function getEmailVisibile(): ?bool
    {
        return $this->email_visibile;
    }

    /**
     * @param bool|null $email_visibile
     */
    public function setEmailVisibile(?bool $email_visibile): void
    {
        $this->email_visibile = $email_visibile;
    }

    /**
     * @return string
     */
    public function getUserPass(): string
    {
        return $this->userPass;
    }

    /**
     * @param string $userPass
     */
    public function setUserPass(string $userPass): void
    {
        $this->userPass = $userPass;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->fname;
    }

    /**
     * @param string|null $fname
     */
    public function setFirstName(?string $fname): void
    {
        $this->fname = $fname;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lname;
    }

    /**
     * @param string|null $lname
     */
    public function setLastName(?string $lname): void
    {
        $this->lname = $lname;
    }

    /**
     * @return \DateTime
     */
    public function getRegDate(): \DateTime
    {
        return $this->reg_date;
    }

    /**
     * @param \DateTime $reg_date
     */
    public function setRegDate(\DateTime $reg_date): void
    {
        $this->reg_date = $reg_date;
    }

    /**
     * @return \DateTime
     */
    public function getLastOnline(): \DateTime
    {
        return $this->last_online;
    }

    /**
     * @param \DateTime $last_online
     */
    public function setLastOnline(\DateTime $last_online): void
    {
        $this->last_online = $last_online;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return ArrayCollection|null
     */
    public function getUPubs(): ?ArrayCollection
    {
        return $this->u_pubs;
    }

    /**
     * @param ArrayCollection|null $u_pubs
     */
    public function setUPubs(?ArrayCollection $u_pubs): void
    {
        $this->u_pubs = $u_pubs;
    }

    /**
     * @return ArrayCollection|null
     */
    public function getUForums(): ?ArrayCollection
    {
        return $this->u_forums;
    }

    /**
     * @param ArrayCollection|null $u_forums
     */
    public function setUForums(?ArrayCollection $u_forums): void
    {
        $this->u_forums = $u_forums;
    }

    /**
     * @return PersistentCollection|null
     */
    public function getBilling(): ?PersistentCollection
    {
        return $this->billing;
    }

    /**
     * @param PersistentCollection|null $billing
     */
    public function setBilling(?PersistentCollection $billing): void
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

    /**
     * @return bool|null
     */
    public function getSubscribed(): ?bool
    {
        return $this->subscribed;
    }

    /**
     * @param bool|null $subscribed
     */
    public function setSubscribed(?bool $subscribed): void
    {
        $this->subscribed = $subscribed;
    }

    /**
     * @return bool
     */
    public function isHideSubpanel(): bool
    {
        return $this->hide_subpanel;
    }

    /**
     * @param bool $hide_subpanel
     */
    public function setHideSubpanel(bool $hide_subpanel): void
    {
        $this->hide_subpanel = $hide_subpanel;
    }
    //</editor-fold>
}