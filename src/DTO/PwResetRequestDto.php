<?php
namespace App\DTO;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class PwResetRequestDto {
    private string $email;

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getForm(FormFactoryInterface $formFactory): FormInterface {
        $builder = $formFactory->createBuilder(FormType::class, $this);
        $builder->add('email', EmailType::class, ['required'=>true, 'label'=>"Email:"]);
        $builder->add('Elküldés', SubmitType::class, ['attr'=>['style'=>"width: 100%;"]]);
        return $builder->getForm();
    }
}