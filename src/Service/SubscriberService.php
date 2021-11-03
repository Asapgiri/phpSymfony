<?php
namespace App\Service;

use App\DTO\TempFileDto;
use App\Entity\Subscriber;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\File;

class SubscriberService extends CrudService implements SubscriberServiceInterface {
    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory)
    {
        parent::__construct($em, $formFactory);
    }

    public function getRepo(): EntityRepository
    {
        return $this->em->getRepository(Subscriber::class);
    }

    public function subscribe(string $email): Subscriber
    {
        $subscriber = $this->getRepo()->findOneBy(['email'=>$email]);
        if ($subscriber) throw new \Exception("Ez az email már feliratkozott.");
        $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$email]);
        if ($user) {
            $user->setSubscribed(true);
            $user->setHideSubpanel(true);
            $this->em->persist($user);
        }
        $subscriber = new Subscriber($email);
        $this->em->persist($subscriber);
        $this->em->flush();
        return $subscriber;
    }

    public function unsubscribe(string $email): void
    {
        $subscriber = $this->getRepo()->findOneBy(['email'=>$email]);
        if (!$subscriber) throw new \Exception("Az email nem található.");
        $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$email]);
        if ($user) {
            $user->setSubscribed(false);
            $this->em->persist($user);
        }
        $this->em->remove($subscriber);
        $this->em->flush();
    }

    public function unsubscribeByToken(string $token): string
    {
        /** @var Subscriber $subscriber */
        $subscriber = $this->getRepo()->findOneBy(['token'=>$token]);
        if (!$subscriber) throw new \Exception("Az email nem található.");
        $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$subscriber->getEmail()]);
        if ($user) {
            $user->setSubscribed(false);
            $this->em->persist($user);
        }
        $this->em->remove($subscriber);
        $this->em->flush();
        return $subscriber->getEmail();
    }

    public function getSubscribers(): ?array
    {
        return $this->getRepo()->findAll();
    }

    public function getOneSubscriber(string $email): ?Subscriber
    {
        return $this->getRepo()->findOneBy(['email'=>$email]);
    }

    public function getForm(TempFileDto $dto): FormInterface
    {
        $builder = $this->formFactory->createBuilder(FormType::class, $dto);
        $builder->add('subject', TextType::class);
        $builder->add('tempFile', FileType::class, [
            'label' => "Elküldendő fájl feltöltése",
            'constraints' => [
                new File([
                    'mimeTypes' => [
                        "text/twig",
                        "text/html",
                        "text/txt"
                    ],
                    'mimeTypesMessage' => "Elfogadott formátumok: twig, html, vagy txt."
                ])
            ],
            'attr' => [
                'accept' => ".twig,.html,.htm,.txt"
            ],
            'help' => "Elfogadott formátumok: twig, html, vagy txt."
        ]);
        $builder->add('is_test', CheckboxType::class, ['label'=>"Teszt", 'required'=>false, 'data'=>true]);
        $builder->add('Elküldés', SubmitType::class);
        return $builder->getForm();
    }
}