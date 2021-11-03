<?php
namespace App\DataFixtures;

use App\Entity\AdUnit;
use App\Entity\Forum;
use App\Entity\Message;
use App\Entity\Permission;
use App\Entity\Publication;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture implements ContainerAwareInterface {
    /** @var string */
    private $environment; // DEV, Test

    /** @var EntityManager */
    private $em;

    /** @var ContainerInterface */
    private $container;

    /** @var UserPasswordEncoderInterface */
    private $encoder;

    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $kernel = $this->container->get('kernel');
        if ($kernel) $this->environment = $kernel->getEnvironment();
    }

    public function load(ObjectManager $manager) {
        echo "HELLO FIXITURES\n\n";
        $this->em = $manager;

        $stackLogger = new DebugStack();
        $this->em->getConnection()->getConfiguration()->setSQLLogger($stackLogger);

        // Adding users::
        $admin = new User();
        $admin->setUserName("admin");
        $admin->setEmail("postmaster@dunafenye.hu");
        $admin->setLastName('Page');
        $admin->setFirstName('Admin');
        $admin->setUserPass($this->encoder->encodePassword($admin, "adminpass"));
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setRegDate(new \DateTime());
        $admin->setLastOnline(new \DateTime());
        $this->em->persist($admin);
        $this->em->flush();

        $deletedUser = new User();
        $deletedUser->setRoles(["ROLE_USER"]);
        $deletedUser->setUAvatar(null);
        $deletedUser->setDescription(null);
        $deletedUser->setEmailVisibile(false);
        $deletedUser->setEmail("N/A");
        $deletedUser->setLastName(null);
        $deletedUser->setFirstName(null);
        $deletedUser->setUserPass("N/A");
        $deletedUser->setRegDate(new \DateTime());
        $deletedUser->setLastOnline(new \DateTime());
        $deletedUser->setUserName('töröltFelhasználó');
        $this->em->persist($deletedUser);
        $this->em->flush();

        /*
        $joe = new User();
        $joe->setUserName("joe");
        $joe->setFirstName("Joe");
        $joe->setLastName("Not");
        $joe->setEmail("joe123@gmail.hu");
        $joe->setUserPass($this->encoder->encodePassword($joe, "joepass"));
        $joe->setRoles(["ROLE_USER"]);
        $joe->setRegDate(new \DateTime());
        $joe->setLastOnline(new \DateTime());
        $this->em->persist($joe);
        $this->em->flush();


        $publication1 = new Publication();
        $publication1->setPubAuthor($admin);
        $publication1->setPubName("Február");
        $publication1->setPubRoute("2021/Mar");
        $publication1->setPubVisible(true);
        $this->em->persist($publication1);
        $this->em->flush();

        $forum1 = new Forum();
        $forum1->setFAuthor($admin);
        $forum1->setFName("First publication");
        $forum1->setFVisible(true);
        $this->em->persist($forum1);
        $this->em->flush();

        $forum2 = new Forum();
        $forum2->setFAuthor($admin);
        $forum2->setFName("Second publication...");
        $forum2->setFVisible(false);
        $this->em->persist($forum2);
        $this->em->flush();

        $msg1 = new Message();
        $msg1->setMsgAuthor($admin);
        $msg1->setMsgForum($forum1);
        $msg1->setMsgText("The first publication is out!!");
        $this->em->persist($msg1);
        $this->em->flush();

        $msg2 = new Message();
        $msg2->setMsgAuthor($joe);
        $msg2->setMsgForum($forum1);
        $msg2->setMsgText("Thats so nice!!");
        $this->em->persist($msg2);
        $this->em->flush();*/

        $ad1 = new AdUnit("U1 (A4)", 184, 268, "mm", 63000, $admin);
        $ad2 = new AdUnit("U2", 184, 134, "mm", 32000, $admin);
        $ad3 = new AdUnit("U3", 184, 67, "mm", 17000, $admin);
        $ad4 = new AdUnit("U4", 155, 67, "mm", 6750, $admin);
        $ad5 = new AdUnit("U5", 65, 67, "mm", 3000, $admin);
        $ad6 = new AdUnit("U6", 184, 80, "mm", 25000, $admin);
        $ad7 = new AdUnit("U7", 184, 30, "mm", 8650, $admin);
        $ad8 = new AdUnit("U8", 90, 142, "mm", 16000, $admin);
        $ad9 = new AdUnit("U9", 90, 30, "mm", 1650, $admin);
        $ad10 = new AdUnit("U10", 90, 45, "mm", 4350, $admin);
        $ad11 = new AdUnit("U11", 90, 60, "mm", 5250, $admin);

        $this->em->persist($ad1);
        $this->em->persist($ad2);
        $this->em->persist($ad3);
        $this->em->persist($ad4);
        $this->em->persist($ad5);
        $this->em->persist($ad6);
        $this->em->persist($ad7);
        $this->em->persist($ad8);
        $this->em->persist($ad9);
        $this->em->persist($ad10);
        $this->em->persist($ad11);
        $this->em->flush();


        echo "QUERIES FINISHED SUCCESSFULLY. ".count($stackLogger->queries)." db\n\n";
    }
    /*
     * php bin/console doctrine:database:create
     *
     * php bin/console doctrine:schema:drop --force --full-database
     * php bin/console doctrine:schema:update --force
     *
     * php bin/console doctrine:fixtures:load --no-interaction -vvv
     */
}
