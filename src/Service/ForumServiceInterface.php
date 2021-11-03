<?php
namespace App\Service;

use App\DTO\ForumDto;
use App\Entity\Forum;
use App\Entity\Message;
use App\Entity\User;
use Symfony\Component\Form\FormInterface;

interface ForumServiceInterface {
    /**
     * @return iterable|Forum[]
     */
    public function getAllForums(): iterable;

    /**
     * @param int $forumId
     * @return Forum
     */
    public function getForumById(int $forumId): Forum;

    /**
     * @param int $userId
     * @return iterable|Forum[]
     */
    public function getForumsByUser(int $userId): iterable;

    /**
     * @param int $numDb
     * @return iterable
     */
    public function getAllVisibleForums(int $numDb = 0): iterable;

    /**
     * @param User $user
     * @param ForumDto $dto
     * @return bool
     */
    public function addForum(User $user, ForumDto $dto): bool;

    /**
     * @param Forum $forum
     * @return bool
     */
    public function delForum(Forum $forum): bool;

    /**
     * @param Forum $oneForum
     * @return FormInterface
     */
    public function getForm(ForumDto $oneForum, bool $canAddInvis = false): FormInterface;

    /**
     * @param Forum $forum
     */
    public function updateForum(Forum $forum): void;

    public function getPage(bool $admin, int $pageNum, int $numberPerPage = 10): array;

    public function isPagable(bool $admin, int $currentPage = 0, int $numberPerPage = 10): ?array;

    public function isLastPage(bool $admin, int $currentPage, int $numberPerPage = 10): bool;

    public function lastPageNumber(bool $admin, int $numberPerPage = 10): int;

    public function count(bool $admin = true): int;
}