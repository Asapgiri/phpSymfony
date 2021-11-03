<?php
namespace App\Entity;

use \Doctrine\ORM\Mapping as ORM;

/**
 * Class Subscriber
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="subscribers")
 */
class Subscriber {
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=254, nullable=false)
     */
    private string $email;

    /**
     * @var string
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private string $token;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private \DateTime $subscribed;

    /**
     * Subscriber constructor.
     * @param string $email
     */
    public function __construct(string $email)
    {
        $this->email = $email;
        $this->subscribed = new \DateTime();
        $this->setToken();
    }

    public function __toString(): string
    {
        return $this->email;
    }

    //<editor-fold desc="Gettes and Setters">
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
     * @return \DateTime
     */
    public function getSubscribed(): \DateTime
    {
        return $this->subscribed;
    }

    /**
     * @param \DateTime $subscribed
     */
    public function setSubscribed(\DateTime $subscribed): void
    {
        $this->subscribed = $subscribed;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(): void
    {
        $this->token = sha1(random_bytes(50));
    }
    //</editor-fold>
}