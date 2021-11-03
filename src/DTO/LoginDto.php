<?php


namespace App\DTO;


use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class LoginDto extends DtoBase {
    /** @var string  */
    private $userName = "";

    /** @var string  */
    private $userPass = "";

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     */
    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    /**
     * @return string
     */
    public function getUserPass(): string
    {
        return $this->userPass;
    }

    /**
     * @param string $userPass
     */
    public function setUserPass(string $userPass): void
    {
        $this->userPass = $userPass;
    }

    public function __construct(FormFactoryInterface $formFactory, Request $request)
    {
        parent::__construct($formFactory, $request);
    }

    public function getForm(): FormInterface
    {
        $builder = $this->formFactory->createBuilder(FormType::class, $this);
        $builder->add('userName', TextType::class, ['attr'=>['placeholder'=>"Felhasználónév"], 'label'=>false]);
        $builder->add('userPass', PasswordType::class, ['attr'=>['placeholder'=>"Jelszó", 'autocomplete'=>"current-password"], 'label'=>false]);
        $builder->add('Bejelentkezés', SubmitType::class, ['attr'=>['class'=>"form-control btn-primary"]]);
        return $builder->getForm();
    }


}