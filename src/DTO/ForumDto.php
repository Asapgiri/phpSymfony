<?php
namespace App\DTO;

class ForumDto {
    /** @var string */
    private $forum_name = "";
    /** @var bool */
    private $forum_visibility = true;
    /** @var string|null  */
    private $description;

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
     * @return string
     */
    public function getForumName(): string
    {
        return $this->forum_name;
    }

    /**
     * @param string $forum_name
     */
    public function setForumName(string $forum_name): void
    {
        $this->forum_name = $forum_name;
    }

    /**
     * @return bool
     */
    public function isForumVisibility(): bool
    {
        return $this->forum_visibility;
    }

    /**
     * @param bool $forum_visibility
     */
    public function setForumVisibility(bool $forum_visibility): void
    {
        $this->forum_visibility = $forum_visibility;
    }
}