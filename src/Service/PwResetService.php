<?php
namespace App\Service;

use App\DTO\PwResetDto;
use App\Entity\PwResetToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PwResetService extends CrudService implements PwResetServiceInterface {
    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory)
    {
        parent::__construct($em, $formFactory);
    }

    public function getRepo(): EntityRepository
    {
        return $this->em->getRepository(PwResetToken::class);
    }

    public function createToken(User $user): PwResetToken
    {
        $token = new PwResetToken($user);
        $token->generatePwrToken();
        $this->em->persist($token);
        $this->em->flush();
        return $token;
    }

    public function validateUserToken(?string $token): ?PwResetToken
    {
        if (!$token) return null;
        $token = $this->getRepo()->findOneBy(['pwr_token'=>$token]);
        if (!$token) return null;
        return $token;
    }

    public function deleteExpieredTokens(): ?int
    {
        $tokens = $this->getRepo()->findAll();
        $dateNow = new \DateTime();
        $deleted = 0;
        /** @var PwResetToken $token */
        foreach ($tokens as &$token) {
            if ($token->getPwrValidTo() < $dateNow) {
                $this->em->remove($token);
                $deleted++;
            }
        }
        $this->em->flush();
        return $deleted;
    }

    public function deleteToken(PwResetToken $token): void
    {
        $this->em->remove($token);
        $this->em->flush();
    }

    public function getForm(PwResetDto $dto): FormInterface
    {
        $builder = $this->formFactory->createBuilder(FormType::class, $dto);
        $builder->add('new_password', RepeatedType::class, [
            'required' => true,
            'type' => PasswordType::class,
            'invalid_message' => "A jelszavaknak meg kell egyeznie!",
            'first_options' => [ 'label' => "Új jelszó:"],
            'second_options' => [ 'label' => "Jelszó újra:"],
            'constraints' => [
                new NotBlank(['message' => "A jelszó nem lehet üres!"]),
                new Length([
                    'min' => 6,
                    'minMessage' => "A jelszó minimum hósszúsága {{ limit }} karakter",
                    'max' => 4096
                ])
            ]
        ]);
        $builder->add('Jelszó cseréje', SubmitType::class, ['attr'=>['style'=>"width: 100%;"]]);
        return $builder->getForm();
    }
}