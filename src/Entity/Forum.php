<?php
namespace App\Entity;

use \Doctrine\ORM\Mapping as ORM;

/**
 * Class Forum
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="forums")
 * @ORM\HasLifecycleCallbacks
 */
class Forum {
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $f_id;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $f_name;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $f_created;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $f_lastmsg;

    /**
     * @var User|null
     * @ORM\JoinColumn(name="f_author", referencedColumnName="user_id")
     * @ORM\ManyToOne(targetEntity="User", inversedBy="u_forums")
     */
    private $f_author;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $f_visible = true;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $f_description;

    /**
     * @return string|null
     */
    public function getFDescription(): ?string
    {
        return $this->f_description;
    }

    /**
     * @param string|null $f_description
     */
    public function setFDescription(?string $f_description): void
    {
        $this->f_description = $f_description;
    }

    /**
     * @return bool
     */
    public function getFVisible(): bool
    {
        return $this->f_visible;
    }

    /**
     * @param bool $f_visible
     */
    public function setFVisible(bool $f_visible): void
    {
        $this->f_visible = $f_visible;
    }

    /**
     * @return int
     */
    public function getFId(): int
    {
        return $this->f_id;
    }

    /**
     * @return string|null
     */
    public function getFName(): ?string
    {
        return $this->f_name;
    }

    /**
     * @param string|null $f_name
     */
    public function setFName(?string $f_name): void
    {
        $this->f_name = $f_name;
    }

    /**
     * @return \DateTime|null
     */
    public function getFCreated(): ?\DateTime
    {
        return $this->f_created;
    }

    /**
     * @param \DateTime|null $f_created
     */
    public function setFCreated(?\DateTime $f_created): void
    {
        $this->f_created = $f_created;
    }

    /**
     * @return \DateTime|null
     */
    public function getFLastmsg(): ?\DateTime
    {
        return $this->f_lastmsg;
    }

    /**
     * @param \DateTime|null $f_lastmsg
     */
    public function setFLastmsg(?\DateTime $f_lastmsg): void
    {
        $this->f_lastmsg = $f_lastmsg;
    }

    /**
     * @return User|null
     */
    public function getFAuthor(): ?User
    {
        return $this->f_author;
    }

    /**
     * @param User|null $f_author
     */
    public function setFAuthor(?User $f_author): void
    {
        $this->f_author = $f_author;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps() : void {
        $this->f_lastmsg = new \DateTime();
        if ($this->f_created == null) $this->f_created = new \DateTime();
    }
}