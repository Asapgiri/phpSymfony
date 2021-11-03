<?php
namespace App\Service;

use App\DTO\PublicationDto;
use App\Entity\Publication;
use App\Entity\User;
use Symfony\Component\Form\FormInterface;

interface PublicationServiceInterface {
    /**
     * @return Publication|array
     */
    public function getAllPublications(): array;

    /**
     * @param int $numDb
     * @return Publication|array
     */
    public function getVisiblePublications(int $numDb = 0): array;

    /**
     * @param int $pubId
     * @return Publication
     */
    public function getPublicationById(int $pubId): Publication;

    /**
     * @param int $userId
     * @return Publication|array
     */
    public function getPublicationByUser(int $userId): array;

    /**
     * @param PublicationDto $dto
     * @param User $user
     * @return bool
     */
    public function addPublication(PublicationDto $dto, User $user): bool;

    /**
     * @param int $pubId
     * @return bool
     */
    public function delPublication(int $pubId): bool;

    /**
     * @param int $pubId
     * @return string
     */
    public function getPublicationRoute(int $pubId): string;

    /**
     * @param PublicationModel $pubModel
     * @return FormInterface
     */
    public function getForm(PublicationDto $pubModel): FormInterface;

    /**
     * @param Publication $pub
     */
    public function updatePublication(Publication $pub): void;

    /**
     * @param Publication $pub
     * @return FormInterface
     */
    public function getEditorForm(Publication $pub): FormInterface;
}