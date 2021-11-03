<?php
namespace App\Service;

use App\DTO\UserImageDto;
use App\Entity\AdUnit;
use App\Entity\Advertisement;
use App\Entity\Forum;
use App\Entity\Message;
use App\Entity\Publication;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;

class UserService extends CrudService implements UserServiceInterface {
    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory) {
        parent::__construct($em, $formFactory);
    }

    public function getRepo(): EntityRepository {
        return $this->em->getRepository(User::class);
    }

    public function getAllUsers(): array {
        return $this->getRepo()->findAll();
    }

    public function getUser(string $name): ?User {
        return $this->getRepo()->findOneBy(['userName'=>$name]);
    }

    public function getUserById(int $userId): ?User {
        return $this->getRepo()->findOneBy(['userId'=>$userId]);
    }

    public function addUser(User $user) {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function delUser(int $userId) {
        $deletedUser = $this->getUser('töröltFelhasználó');
        $userToDelete = $this->getUserById($userId);

        $adunits = $this->em->getRepository(AdUnit::class)->findBy(['unit_author'=>$userToDelete]);
        foreach ($adunits as &$unit) {
            $unit->setUnitAuthor($deletedUser);
            $this->em->persist($unit);
        }

        $advertisements = $this->em->getRepository(Advertisement::class)->findBy(['ad_author'=>$userToDelete]);
        foreach ($advertisements as &$ad) {
            $ad->setAdAuthor($deletedUser);
            $this->em->persist($ad);
        }

        $forums = $this->em->getRepository(Forum::class)->findBy(['f_author'=>$userToDelete]);
        foreach ($forums as &$forum) {
            $forum->setFAuthor($deletedUser);
            $this->em->persist($forum);
        }

        $messages = $this->em->getRepository(Message::class)->findBy(['msg_author'=>$userToDelete]);
        foreach ($messages as &$msg) {
            $msg->setMsgAuthor($deletedUser);
            $this->em->persist($msg);
        }

        $pubs = $this->em->getRepository(Publication::class)->findBy(['pub_author'=>$userToDelete]);
        foreach ($pubs as &$pub) {
            $pub->setPubAuthor($deletedUser);
            $this->em->persist($pub);
        }

        $this->em->remove($userToDelete);
        $this->em->flush();
    }

    public function getForm(User $user, bool $editorIsAbdmin = false): FormInterface {
        $form = $this->formFactory->createBuilder(FormType::class, $user);
        if ($editorIsAbdmin) {
            $form->add('userName', TextType::class, [
                'label'=>"Felhasználó",
                'attr' => ['minlength' => "3", 'maxlength' => "55"],
                'constraints' => [new Length(['min' => 3, 'max' => 55])]
            ]);
            $form->add('roles', ChoiceType::class, [
                'label'=>"Rang",
                'choices' => [
                    'Adminisztrátor' => "ROLE_ADMIN",
                    'Moderátor' => "ROLE_MOD",
                    'Felhasználó' => "ROLE_USER"
                ]
            ]);
            $form->get('roles')->addModelTransformer(
                new CallbackTransformer(
                    function ($rolesArray) {
                        // transform the array to a string
                        return count($rolesArray)? $rolesArray[0]: null;
                    },
                    function ($rolesString) {
                        // transform the string back to an array
                        return [$rolesString];
                    }
                )
            );
        }
        else $form->add('userName', TextType::class, ['label'=>"Felhasználó", 'constraints' => [new Length(['min' => 3, 'max' => 55])], 'attr'=>['class'=>"px-2 form-control-plaintext", 'readonly'=>true]]);
        $form->add('telephone', TelType::class, ['required'=>false, 'label'=>"Telefon",
            'attr' => [
                'style' => "width: 250px;",
                'minlength' => "15",
                'maxlength' => "15",
                'onkeyup' => "telCheck(event, this.value)",
            ]
        ]);
        $form->add('email', TextType::class, ['required' => true, 'attr' => ['minlength' => "3", 'maxlength' => "254"], 'constraints' => [new Length(['min' => 3, 'max' => 254])]]);
        $form->add('email_visibile', CheckboxType::class, ['required' => false, 'value'=>false, 'label'=>"Email látható"]);
        $form->add('lastname', TextType::class, ['required' => false, 'label'=>"Vezetéknév", 'attr' => ['maxlength' => "100"], 'constraints' => [new Length(['max' => 100])]]);
        $form->add('firstname', TextType::class, ['required' => false, 'label'=>"Keresztnév", 'attr' => ['maxlength' => "100"], 'constraints' => [new Length(['max' => 100])]]);
        $form->add('description', TextareaType::class, [
            'required' => false, 'label'=>"Leírás",
            'attr' => [
                'oninput' => "if (this.scrollHeight < 300) {this.style.height = ``;this.style.height = this.scrollHeight + 3 + `px`}",
                'minlength' => "3",
                'maxlength' => "5000"
            ],
            'constraints' => [new Length(['max' => 5000])]
        ]);

        $form->add('Módosítás', SubmitType::class, ['attr'=>['style'=>"width: 300px; margin: 30px 0px;"]]);

        return $form->getForm();
    }

    public function editUser(User $user, User $editor) {
        if (in_array('ROLE_ADMIN', $editor->getRoles())) {
            if (count($this->getRepo()->findBy(['userName'=>$user->getUsername()])) > 1) {
                throw new Exception("Username already in use.");
            }
            else $this->addUser($user);
        }
        else {
            $editor->setFirstName($user->getFirstName());
            $editor->setLastName($user->getLastName());
            $editor->setEmail($user->getEmail());
            $editor->setEmailVisibile($user->getEmailVisibile());
            $editor->setDescription($user->getDescription());
            $editor->setUAvatar($user->getUAvatar());
            $this->addUser($editor);
        }
    }

    public function getPfpForm(UserImageDto $user): FormInterface
    {
        $form = $this->formFactory->createBuilder(FormType::class, $user);
        $form->add('u_avatar', FileType::class, ['required'=>false, 'label'=>"Profilkép:", 'constraints'=>[
            new File([
                'mimeTypes' => [
                    "image/jpeg",
                    "image/png",
                    "image/jpg"
                ],
                'mimeTypesMessage' => "Kérem egy jpg/jpeg/png fájlt töltsön fel."
            ])
        ]]);
        $form->add('Feltöltés', SubmitType::class);
        return $form->getForm();
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->getRepo()->findOneBy(['email'=>$email]);
    }
}