<?php
namespace App\Entity;

use \Doctrine\ORM\Mapping as ORM;

/**
 * Class Publication
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="pwreset_tokens")
 */
class PwResetToken {
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $pwr_id;

    /**
     * @var User
     * @ORM\JoinColumn(name="pwr_user", referencedColumnName="user_id")
     * @ORM\ManyToOne(targetEntity="User")
     */
    private User $pwr_user;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private string $pwr_token;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private \DateTime $pwr_created;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private \DateTime $pwr_valid_to;

    /**
     * PwResetToken constructor.
     * @param User $pwr_user
     * @param \DateTime $pwr_valid_to
     * @param \DateTime $pwr_created
     */
    public function __construct(User $pwr_user, \DateTime $pwr_valid_to = null, \DateTime $pwr_created = null)
    {
        $this->pwr_user = $pwr_user;

        if ($pwr_created) $this->pwr_created = $pwr_created;
        else $this->pwr_created = new \DateTime();

        if ($pwr_valid_to) $this->pwr_valid_to = $pwr_valid_to;
        else $this->pwr_valid_to = new \DateTime("+30 min");
    }

    /**
     * @return int
     */
    public function getPwrId(): int
    {
        return $this->pwr_id;
    }

    /**
     * @return User
     */
    public function getPwrUser(): User
    {
        return $this->pwr_user;
    }

    /**
     * @param User $pwr_user
     */
    public function setPwrUser(User $pwr_user): void
    {
        $this->pwr_user = $pwr_user;
    }

    /**
     * @return string
     */
    public function getPwrToken(): string
    {
        return $this->pwr_token;
    }

    /**
     * @param string $pwr_token
     */
    public function setPwrToken(string $pwr_token): void
    {
        $this->pwr_token = $pwr_token;
    }

    /**
     * @return \DateTime
     */
    public function getPwrCreated(): \DateTime
    {
        return $this->pwr_created;
    }

    /**
     * @param \DateTime $pwr_created
     */
    public function setPwrCreated(\DateTime $pwr_created): void
    {
        $this->pwr_created = $pwr_created;
    }

    /**
     * @return \DateTime
     */
    public function getPwrValidTo(): \DateTime
    {
        return $this->pwr_valid_to;
    }

    /**
     * @param \DateTime $pwr_valid_to
     */
    public function setPwrValidTo(\DateTime $pwr_valid_to): void
    {
        $this->pwr_valid_to = $pwr_valid_to;
    }

    public function generatePwrToken(): void
    {
        $this->pwr_token = sha1(random_bytes(50));
    }
}