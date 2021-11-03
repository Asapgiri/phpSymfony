<?php
namespace App\DTO;

class PwResetDto {
    private string $new_password;

    /**
     * @return string
     */
    public function getNewPassword(): string
    {
        return $this->new_password;
    }

    /**
     * @param string $new_password
     */
    public function setNewPassword(string $new_password): void
    {
        $this->new_password = $new_password;
    }
}