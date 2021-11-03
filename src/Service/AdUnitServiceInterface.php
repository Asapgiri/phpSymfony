<?php
namespace App\Service;

use App\Entity\AdUnit;

interface AdUnitServiceInterface {
    /**
     * @return AdUnit|array
     */
    public function getAll(): array;

    /**
     * @param int $id
     * @return AdUnit|null
     */
    public function getOneById(int $id): AdUnit;
}