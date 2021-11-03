<?php
namespace App\DTO;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PwChangeDto {
    private string $oldPassword;
    private string $newPassword;

    /**
     * @return string
     */
    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    /**
     * @param string $newPassword
     */
    public function setNewPassword(string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    /**
     * @return string
     */
    public function getOldPassword(): string
    {
        return $this->oldPassword;
    }

    /**
     * @param string $oldPassword
     */
    public function setOldPassword(string $oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

    public function getForm(FormFactoryInterface $formFactory): FormInterface {
        $form = $formFactory->createBuilder(FormType::class, $this);
        $form->add('oldPassword', PasswordType::class, ['required'=>true, 'label'=>"Aktuális jelszó:"]);
        $form->add('newPassword', RepeatedType::class, [
            'required' => true,
            'type' => PasswordType::class,
            'invalid_message' => "A jelszavaknak meg kell egyeznie!",
            'first_options' => [ 'label' => "Új jelszó:", 'attr'=>['autocomplete'=>"new-password"]],
            'second_options' => [ 'label' => "Jelszó újra:", 'attr'=>['autocomplete'=>"new-password"]],
            'constraints' => [
                new NotBlank(['message' => "A jelszó nem lehet üres!"]),
                new Length([
                    'min' => 6,
                    'minMessage' => "A jelszó minimum hósszúsága {{ limit }} karakter",
                    'max' => 4096
                ])
            ]
        ]);
        $form->add('Jelszó módosítás', SubmitType::class, ['attr'=>['style'=>"width: 100%;"]]);
        return $form->getForm();
    }
}