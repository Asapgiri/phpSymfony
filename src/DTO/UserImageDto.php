<?php
namespace App\DTO;

use App\Entity\User;

class UserImageDto {
    /** @var bool */
    private $returned_once = false;

    /** @var mixed|null */
    private $u_avatar;

    /** @var string|null */
    private $u_vatar_stringified;

    /**
     * @return mixed|null
     */
    public function getUAvatar()
    {
        return $this->u_avatar;
    }

    /**
     * @param User $user
     * @return string
     */
    public function getUAvatarSaveChanges(User $user): ?string {
        if (!$this->returned_once) {
            if ($this->u_avatar) {
                if ($user->getUAvatar() && file_exists("users/images/" . $user->getUAvatar())) unlink("users/images/" . $user->getUAvatar());

                $date = date("Ymd_Hsi_");
                $type = explode(".", $this->u_avatar->getClientOriginalName())[1];
                $this->u_avatar->move(
                    "users/images/",
                    $date . $user->getUsername() . '.' . $type
                );
                $this->u_vatar_stringified = $date . $user->getUsername() . '.' . $type;
                $this->returned_once = true;
            }
            else {
                return null;
            }
        }
        return $this->u_vatar_stringified;
    }

    /**
     * @param mixed|null $u_avatar
     */
    public function setUAvatar($u_avatar): void
    {
        $this->u_avatar = $u_avatar;
    }
}