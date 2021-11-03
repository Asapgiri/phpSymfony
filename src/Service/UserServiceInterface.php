<?php
namespace App\Service;

use App\DTO\UserImageDto;
use App\Entity\User;
use Symfony\Component\Form\FormInterface;

interface UserServiceInterface {
    /**
     * @return User|array
     */
    public function getAllUsers(): ?array;

    /**
     * @param string $name
     * @return User|null
     */
    public function getUser(string $name): ?User;

    /**
     * @param int $userId
     * @return User|null
     */
    public function getUserById(int $userId): ?User;

    /**
     * @param User $user
     * @return mixed
     */
    public function addUser(User $user);

    /**
     * @param int $userId
     * @return mixed
     */
    public function delUser(int $userId);

    /**
     * @param User $user
     * @param bool $editorIsAdmin
     * @return FormInterface
     */
    public function getForm(User $user, bool $editorIsAdmin = false): FormInterface;

    /**
     * @param User $user
     * @return FormInterface
     */
    public function getPfpForm(UserImageDto $user): FormInterface;

    /**
     * @param User $user
     * @param User $editor
     * @return mixed
     */
    public function editUser(User $user, User $editor);

    /**
     * @param string $email
     * @return User|null
     */
    public function getUserByEmail(string $email): ?User;
}