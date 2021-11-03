<?php
namespace App\DTO;

use App\Entity\Publication;
use Symfony\Component\Validator\Constraints\DateTime;

class PublicationDto {
    /** @var string */
    private $pub_name = "";
    /** @var string */
    private $pub_route = "";
    /** @var string|null */
    private $pub_text = "";
    /** @var bool */
    private $pub_visible = true;
    /** @var \DateTime|null */
    private $pub_publicate_day;
    /** @var \DateTime|null */
    private $pub_publicate_time;
    /** @var mixed|null */
    private $pub_image;

    /**
     * PublicationDto constructor.
     */
    public function __construct(Publication $pub = null) {
        if ($pub != null) {
            $this->pub_name = $pub->getPubName();
            $this->pub_route = $pub->getPubRoute();
            $this->pub_visible = $pub->isPubVisible();
            $this->pub_text = $pub->getPubText();
        }
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
     * @return mixed|null
     */
    public function getPubImage()
    {
        return $this->pub_image;
    }

    /**
     * @param mixed|null $pub_image
     */
    public function setPubImage($pub_image): void
    {
        $this->pub_image = $pub_image;
    }

    /**
     * @return \DateTime|null
     */
    public function getPubPublicateDay(): ?\DateTime
    {
        return $this->pub_publicate_day;
    }

    /**
     * @param \DateTime|null $pub_publicate_day
     */
    public function setPubPublicateDay(?\DateTime $pub_publicate_day): void
    {
        $this->pub_publicate_day = $pub_publicate_day;
    }

    /**
     * @return \DateTime|null
     */
    public function getPubPublicateTime(): ?\DateTime
    {
        return $this->pub_publicate_time;
    }

    /**
     * @param \DateTime|null $pub_publicate_time
     */
    public function setPubPublicateTime(?\DateTime $pub_publicate_time): void
    {
        $this->pub_publicate_time = $pub_publicate_time;
    }

    /**
     * @return string
     */
    public function getPubName(): string
    {
        return $this->pub_name;
    }

    /**
     * @param string $pub_name
     */
    public function setPubName(string $pub_name): void
    {
        $this->pub_name = $pub_name;
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
}