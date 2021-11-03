<?php
namespace App\Entity;

use \Doctrine\ORM\Mapping as ORM;

/**
 * Class Message
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="messages")
 * @ORM\HasLifecycleCallbacks
 */
class Message {
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $msg_id;

    /**
     * @var User|null
     * @ORM\JoinColumn(name="msg_author", referencedColumnName="user_id")
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $msg_author;

    /**
     * @var Forum|null
     * @ORM\JoinColumn(name="msg_forum", referencedColumnName="f_id")
     * @ORM\ManyToOne(targetEntity="Forum")
     */
    private $msg_forum;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $msg_created;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $msg_modified;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=5000, nullable=true)
     */
    private $msg_text;

    /**
     * @return int
     */
    public function getMsgId(): int
    {
        return $this->msg_id;
    }

    /**
     * @return User|null
     */
    public function getMsgAuthor(): ?User
    {
        return $this->msg_author;
    }

    /**
     * @param User|null $msg_author
     */
    public function setMsgAuthor(?User $msg_author): void
    {
        $this->msg_author = $msg_author;
    }

    /**
     * @return Forum|null
     */
    public function getMsgForum(): ?Forum
    {
        return $this->msg_forum;
    }

    /**
     * @param Forum|null $msg_forum
     */
    public function setMsgForum(?Forum $msg_forum): void
    {
        $this->msg_forum = $msg_forum;
    }

    /**
     * @return \DateTime|null
     */
    public function getMsgCreated(): ?\DateTime
    {
        return $this->msg_created;
    }

    /**
     * @param \DateTime|null $msg_created
     */
    public function setMsgCreated(?\DateTime $msg_created): void
    {
        $this->msg_created = $msg_created;
    }

    /**
     * @return \DateTime|null
     */
    public function getMsgModified(): ?\DateTime
    {
        return $this->msg_modified;
    }

    /**
     * @param \DateTime|null $msg_modified
     */
    public function setMsgModified(?\DateTime $msg_modified): void
    {
        $this->msg_modified = $msg_modified;
    }

    /**
     * @return string|null
     */
    public function getMsgText(): ?string
    {
        return $this->msg_text;
    }

    /**
     * @param string|null $msg_text
     */
    public function setMsgText(?string $msg_text): void
    {
        $this->msg_text = $msg_text;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps() : void {
        $this->msg_modified = new \DateTime();
        if ($this->msg_created == null) $this->msg_created = new \DateTime();
    }
}