<?php
namespace App\DTO;

class TempFileDto {
    /** @var mixed|null */
    private $tempFile;

    /** @var bool */
    private bool $is_test;

    /** @var string */
    private string $subject;

    /**
     * @return mixed|null
     */
    public function getTempFile()
    {
        return $this->tempFile;
    }

    /**
     * @param mixed|null $tempFile
     */
    public function setTempFile($tempFile): void
    {
        $this->tempFile = $tempFile;
    }

    /**
     * @return bool
     */
    public function isIsTest(): bool
    {
        return $this->is_test;
    }

    /**
     * @param bool $is_test
     */
    public function setIsTest(bool $is_test): void
    {
        $this->is_test = $is_test;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }
}