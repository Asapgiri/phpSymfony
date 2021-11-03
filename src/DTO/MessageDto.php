<?php
namespace App\DTO;

class MessageDto {
    /** @var string */
    private $msg_text = "";

    /**
     * @return string
     */
    public function getMsgText(): string
    {
        return $this->msg_text;
    }

    /**
     * @param string $msg_text
     */
    public function setMsgText(string $msg_text): void
    {
        $this->msg_text = $msg_text;
    }
}