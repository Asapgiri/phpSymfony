<?php
namespace App\Service;

interface BillingDataServiceInterface {
    public function getAll(): array;
    public function getOneById(int $id);
}