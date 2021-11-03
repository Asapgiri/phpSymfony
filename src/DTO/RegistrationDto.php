<?php
namespace App\DTO;


use PhpParser\Node\Stmt\Label;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationDto extends DtoBase {
    /** @var static */
    private $userName = "";
    /** @var string|null */
    private $firstName;
    /** @var string|null */
    private $lastName;
    /** @var string */
    private $email = "";
    /** @var string */
    private $clearPass = "";
    /** @var bool */
    private $gdprAgreed = false;

    public function __construct(FormFactoryInterface $formFactory, Request $request)
    {
        parent::__construct($formFactory, $request);
    }

    public function getForm(): FormInterface
    {
        $builder = $this->formFactory->createBuilder(FormType::class, $this);
        $builder->add('userName', TextType::class, [ 'required' => true, 'label' => "Felhasználónév", 'row_attr' => [ 'class' => "col-md-12 mb-6" ] ]);
        $builder->add('email', EmailType::class, [ 'required' => true, 'label' => "Email cím", 'row_attr' => [ 'class' => "col-md-12 mb-6" ] ]);
        $builder->add('lastName', TextType::class, [ 'required' => false, 'label' => "Vezetéknév", 'row_attr' => [ 'class' => "col-md-6 mb-3" ] ]);
        $builder->add('firstName', TextType::class, [ 'required' => false, 'label' => "Keresztnév", 'row_attr' => [ 'class' => "col-md-6 mb-3" ] ]);
        $builder->add('clearPass', RepeatedType::class, [
            'required' => true,
            'type' => PasswordType::class,
            'invalid_message' => "The passwords must match!",
            'first_options' => [ 'label' => "Jelszó", 'row_attr' => [ 'class' => "col-md-6 mb-3" ], 'attr' => [ 'autocomplete' => "new-password" ] ],
            'second_options' => [ 'label' => "Jelszú újra", 'row_attr' => [ 'class' => "col-md-6 mb-3" ], 'attr' => [ 'autocomplete' => "new-password" ] ],
            'constraints' => [
                new NotBlank(['message' => "A jelszó nem lehet üres!"]),
                new Length([
                    'min' => 6,
                    'minMessage' => "A jelszó minimum hósszúsága {{ limit }} karakter",
                    'max' => 4096
                ])
            ]
        ]);

        $builder->add('gdprAgreed', CheckboxType::class, [ 'constraints' => [
                new IsTrue(['message' => "A regisztrációhoz el kell fogadnod az Általános felhasználási feltételekt és az Adatvédelmi szabályzatot!"])
            ],
            'label' => "Elfogadom az {{ aszf }} és az {{ avsz }}!",
            'row_attr' => [ 'class' => "col-md-12" ]
        ]);
        $builder->add('Regisztráció', SubmitType::class, [
            'row_attr'=>['class'=>"col-12 mt-4"],
            'attr'=>['class'=>"form-control btn-primary"]
        ]);
        return $builder->getForm();
    }

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
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

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

    /**
     * @return string
     */
    public function getClearPass(): string
    {
        return $this->clearPass;
    }

    /**
     * @param string $clearPass
     */
    public function setClearPass(string $clearPass): void
    {
        $this->clearPass = $clearPass;
    }

    /**
     * @return bool
     */
    public function isGdprAgreed(): bool
    {
        return $this->gdprAgreed;
    }

    /**
     * @param bool $gdprAgreed
     */
    public function setGdprAgreed(bool $gdprAgreed): void
    {
        $this->gdprAgreed = $gdprAgreed;
    }
}