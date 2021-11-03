<?php
namespace App\Service;

use App\DTO\AdvertisementDto;
use App\Entity\Advertisement;
use Symfony\Component\Form\FormInterface;

interface AdServiceInterface {
    /**
     * @return Advertisement|array
     */
    public function getAll(): array;

    /**
     * @param int $adId
     * @return Advertisement
     */
    public function getAdById(int $adId): Advertisement;

    /**
     * @param AdvertisementDto $dto
     * @return Advertisement
     */
    public function addAd(AdvertisementDto $dto): Advertisement;

    /**
     * @param int $adId
     */
    public function delAd(int $adId): void;

    /**
     * @param AdvertisementDto $dto
     * @return FormInterface
     */
    public function getForm(AdvertisementDto $dto): FormInterface;

    /**
     * @return Advertisement|array
     */
    public function getUnwatchedAds(): array;

    /**
     * @return int
     */
    public function countUnwatchedAds(): int;

    /**
     * @param int $adId
     * @return Advertisement
     */
    public function seenAd(int $adId): Advertisement;
}