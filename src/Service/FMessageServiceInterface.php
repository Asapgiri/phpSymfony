<?php


namespace App\Service;


use App\DTO\MessageDto;
use App\Entity\Forum;
use App\Entity\Message;
use App\Entity\User;
use Symfony\Component\Form\FormInterface;

interface FMessageServiceInterface {
    /**
     * @param int $forumId
     * @return Message[]
     */
    public function getForumMessagesByForumId(int $forumId): array;

    /**
     * @param int $messageId
     * @return Message
     */
    public function getMessageById(int $messageId): Message;

    /**
     * @param User $msgAuthor
     * @return Message[]
     */
    public function getMessagesByAuthor(User $msgAuthor): array;

    /**
     * @param Message $message
     * @return FormInterface
     */
    public function getForm(MessageDto $message): FormInterface;

    /**
     * @param User $author
     * @param string $text
     * @param int $forum
     * @return Message
     */
    public function addMessage(User $author, string $text, int $forumId): Message;

    /**
     * @param int $msgId
     * @return bool
     */
    public function delMessage(int $msgId): bool;

    /**
     * @param Forum $forumId
     * @return int
     */
    public function lastId(Forum $forum): int;

    /**
     * @param Forum $forumId
     * @return int
     */
    public function count(Forum $forum): int;

    /**
     * @param Forum $forum
     * @param int $db
     * @return Message|array
     */
    public function getLastXMessages(Forum $forum, int $db): array;

    /**
     * @param Forum $forum
     * @param int $pageNum
     * @return Message|array
     */
    public function getPage(Forum $forum, int $pageNum, int $numberPerPage = 10): array;

    /**
     * @param int $currentPage
     * @param int $numberPerPage
     * @return array|null
     */
    public function isPagable(Forum $forum, int $currentPage = 0, int $numberPerPage = 10): ?array;

    /**
     * @param Forum $forum
     * @param int $currentPage
     * @return bool
     */
    public function isLastPage(Forum $forum, int $currentPage, int $numberPerPage = 10): bool;

    /**
     * @param Forum $forum
     * @param int $numberPerPage
     * @return int
     */
    public function lastPageNumber(Forum $forum, int $numberPerPage = 10): int;
}