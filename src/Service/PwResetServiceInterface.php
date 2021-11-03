<?php
namespace App\Service;

use App\DTO\PwResetDto;
use App\Entity\PwResetToken;
use App\Entity\User;
use Symfony\Component\Form\FormInterface;

interface PwResetServiceInterface {
    public function createToken(User $user): PwResetToken;
    public function validateUserToken(?string $token): ?PwResetToken;
    public function deleteExpieredTokens(): ?int;
    public function deleteToken(PwResetToken $token): void;
    public function getForm(PwResetDto $dto): FormInterface;
}