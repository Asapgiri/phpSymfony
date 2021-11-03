<?php
namespace App\Entity;

use \Doctrine\ORM\Mapping as ORM;

/**
 * Class Publication
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="publications")
 * @ORM\HasLifecycleCallbacks
 */
class Publication {
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $pub_id;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $pub_name;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $pub_created;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $pub_date;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $pub_last_visited;

    /**
     * @var User|null
     * @ORM\JoinColumn(name="pub_author", referencedColumnName="user_id")
     * @ORM\ManyToOne(targetEntity="User", inversedBy="u_pubs")
     */
    private $pub_author;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $pub_visible = true;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $pub_views = 0;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $pub_route = "";

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $pub_image;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=5000, nullable=true)
     */
    private $pub_text;

    /**
     * @return \DateTime|null
     */
    public function getPubCreated(): ?\DateTime
    {
        return $this->pub_created;
    }

    /**
     * @param \DateTime|null $pub_created
     */
    public function setPubCreated(?\DateTime $pub_created): void
    {
        $this->pub_created = $pub_created;
    }

    /**
     * @return string|null
     */
    public function getPubText(): ?string
    {
        return $this->pub_text;
    }

    /**
     * @param string|null $pub_text
     */
    public function setPubText(?string $pub_text): void
    {
        $this->pub_text = $pub_text;
    }

    /**
     * @return string|null
     */
    public function getPubImage(): ?string
    {
        if ($this->pub_image == null && file_exists('pubs/'.$this->pub_route.'/shot.png')) return '/pubs/'.$this->pub_route.'/shot.png';
        else if ($this->pub_image == null || !file_exists('pubs/images/'.$this->pub_image)) return '/pubs/images/no-image.png';
        else return '/pubs/images/'.$this->pub_image;
    }

    /**
     * @param string|null $pub_image
     */
    public function setPubImage(?string $pub_image): void
    {
        $this->pub_image = $pub_image;
    }

    /**
     * @return string
     */
    public function getPubRoute(): string
    {
        return $this->pub_route;
    }

    /**
     * @param string $pub_route
     */
    public function setPubRoute(string $pub_route): void
    {
        $this->pub_route = $pub_route;
    }

    /**
     * @return int
     */
    public function getPubViews(): int
    {
        return $this->pub_views;
    }

    /**
     * @param int $pub_views
     */
    public function setPubViews(int $pub_views): void
    {
        $this->pub_views = $pub_views;
    }

    /**
     * @return bool
     */
    public function isPubVisible(): bool
    {
        return $this->pub_visible;
    }

    /**
     * @param bool $pub_visible
     */
    public function setPubVisible(bool $pub_visible): void
    {
        $this->pub_visible = $pub_visible;
    }

    /**
     * @return int
     */
    public function getPubId(): int
    {
        return $this->pub_id;
    }

    /**
     * @return string|null
     */
    public function getPubName(): ?string
    {
        return $this->pub_name;
    }

    /**
     * @param string|null $pub_name
     */
    public function setPubName(?string $pub_name): void
    {
        $this->pub_name = $pub_name;
    }

    /**
     * @return \DateTime|null
     */
    public function getPubDate(): ?\DateTime
    {
        return $this->pub_date;
    }

    /**
     * @param \DateTime|null $pub_date
     */
    public function setPubDate(?\DateTime $pub_date): void
    {
        $this->pub_date = $pub_date;
    }

    /**
     * @return \DateTime|null
     */
    public function getPubLastVisited(): ?\DateTime
    {
        return $this->pub_last_visited;
    }

    /**
     * @param \DateTime|null $pub_last_visited
     */
    public function setPubLastVisited(?\DateTime $pub_last_visited): void
    {
        $this->pub_last_visited = $pub_last_visited;
    }

    /**
     * @return User|null
     */
    public function getPubAuthor(): ?User
    {
        return $this->pub_author;
    }

    /**
     * @param User|null $pub_author
     */
    public function setPubAuthor(?User $pub_author): void
    {
        $this->pub_author = $pub_author;
    }

    public function incrementPubViews(): void {
        $this->pub_views++;
    }

    public function isPubVisibleYet(): bool {
        return $this->isPubVisible() && ($this->getPubDate() < new \DateTime());
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps() : void {
        $this->pub_last_visited = new \DateTime();
        if ($this->pub_created == null) $this->pub_created = new \DateTime();
    }
}