<?php
namespace App\Service;

use App\DTO\TempFileDto;
use App\Entity\Subscriber;
use Symfony\Component\Form\FormInterface;

interface SubscriberServiceInterface {
    public function subscribe(string $email): Subscriber;
    public function unsubscribe(string $email): void;
    public function unsubscribeByToken(string $token): string;
    public function getSubscribers(): ?array;
    public function getOneSubscriber(string $email): ?Subscriber;
    public function getForm(TempFileDto $dto): FormInterface;
}