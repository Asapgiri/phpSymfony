<?php
namespace App\Service;

use App\Entity\Advertisement;
use App\Entity\PwResetToken;
use App\Entity\User;

interface MailerServiceInterface {
    public function sendRegisterEmail(User $user): void;
    public function sendPwResetEmail(User $user, PwResetToken $pwToken): void;
    public function sendAdSubmission(Advertisement $ad): void;
    public function sendTemplateEmail(string $recever, string $twig, array $params, string $subject = "Ne válaszoljon!"): void;
    public function sendHirdetEmail(string $recever, string $twig, array $params, bool $test = false, string $subject = "Ne válaszoljon!"): void;
    public function sendTextEmail(string $recever, string $text, string $subject = "Ne válaszoljon!"): void;
}