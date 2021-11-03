<?php
namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SecurityService {
    /** @var EntityManagerInterface */
    private $em;
    /** @var UserPasswordEncoderInterface */
    private $encoder;
    /** @var SubscriberServiceInterface */
    private $subscriberService;

    /**
     * SecurityService constructor.
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     * @param SubscriberServiceInterface $subscriberService
     */
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, SubscriberServiceInterface $subscriberService)
    {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->subscriberService = $subscriberService;
    }

    public function registerUser(string $uname, string $email, string $clearPass, ?string $firstName, ?string $lastName) : ?UserInterface {
        $unameLower = strtolower($uname);
        if (str_contains($unameLower, 'admin')) throw new \Exception("A felhasználónévben nem szerepelhet: admin");
        $email = strtolower($email);
        $userByName = $this->em->getRepository(User::class)->findOneBy(['userName'=>$uname]);
        if ($userByName) throw new \Exception("Ez a felhasználónév már foglalt.");
        $userByEmail = $this->em->getRepository(User::class)->findOneBy(['email'=>$email]);
        if ($userByEmail) throw new \Exception("Ez az email cím már foglalt: ".$email);

        $user = new User();
        $user->setUserName($uname);
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setRoles(["ROLE_USER"]);
        $user->setUserPass($this->encoder->encodePassword($user, $clearPass));
        $user->setRegDate(new \DateTime());
        $user->setLastOnline(new \DateTime());

        $subscriber = $this->subscriberService->getOneSubscriber($email);
        if ($subscriber) {
            $user->setSubscribed(true);
            $user->setHideSubpanel(true);
        }

        //$user->setUOnline(true);
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    public function checkPassword(User $user, string $clearPass) : bool {
        if (!$user) return false;
        return $this->encoder->isPasswordValid($user, $clearPass);
    }

    public function setUserPassword(User $user, string $clearPass): void {
        $user->setUserPass($this->encoder->encodePassword($user, $clearPass));
        $this->em->persist($user);
        $this->em->flush();
    }
}