<?php
namespace App\Service;

use App\DTO\MessageDto;
use App\Entity\Forum;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FMessageService extends CrudService implements FMessageServiceInterface {
    /** @var ForumServiceInterface */
    private $forumService;

    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory, ForumServiceInterface $forumService)
    {
        parent::__construct($em, $formFactory);
        $this->forumService = $forumService;
    }

    public function getRepo(): EntityRepository
    {
        return $this->em->getRepository(Message::class);
    }

    public function getForumMessagesByForumId(int $forumId): array
    {
        return $this->getRepo()->findBy(['msg_forum'=>$forumId]);
    }

    public function getMessageById(int $messageId): Message
    {
        return $this->getRepo()->findOneBy(['msg_id'=>$messageId]);
    }

    public function getMessagesByAuthor(User $msgAuthor): array
    {
        return $this->getRepo()->findBy(['msg_author'=>$msgAuthor]);
    }

    public function getForm(MessageDto $message): FormInterface
    {
        $form = $this->formFactory->createBuilder(FormType::class, $message);
        $form->add('msg_text', TextareaType::class, [ 'required' => true, 'label' => false, 'row_attr' => [ 'class' => "col-10 pr-1" ],
            'attr' => ['oninput' => "if (this.scrollHeight < 150) {this.style.height = ``;this.style.height = this.scrollHeight + 3 + `px`}", 'maxlength' => "5000"]
        ]);
        $form->add('Küldés', SubmitType::class, ['row_attr' => ['class'=>"col-2 pl-1"], 'attr' => ['class'=>"btn-primary btn-block", 'style'=>"height: 100%;"]]);
        return $form->getForm();
    }

    public function addMessage(User $author, string $text, int $forumId): Message
    {
        $forum = $this->forumService->getForumById($forumId);
        $msg = new Message();
        $msg->setMsgText($text);
        $msg->setMsgForum($forum);
        $msg->setMsgAuthor($author);
        $this->em->persist($msg);
        $this->em->flush();

        $forum->setFLastmsg(new \DateTime());
        $this->forumService->updateForum($forum);
        return $msg;
    }

    public function delMessage(int $msgId): bool
    {
        $msg = $this->getRepo()->findOneBy(['msg_id'=>$msgId]);
        $this->em->remove($msg);
        $this->em->flush();
        return true;
    }

    public function lastId(Forum $forum): int
    {
        return $this->em->createQueryBuilder()
            ->select('msg')
            ->from(Message::class, 'msg')
            ->where('msg.msg_forum = :forum')
            ->setParameter('forum', $forum)
            ->orderBy('msg.msg_id', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult()
            ->getMsgId();
    }

    public function count(Forum $forum): int
    {
        return $this->em->createQueryBuilder()
            ->select('count(msg.msg_id)')
            ->from(Message::class, 'msg')
            ->where('msg.msg_forum = :forum')
            ->setParameter('forum', $forum)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getLastXMessages(Forum $forum, int $db): array
    {
        return array_reverse($this->em->createQueryBuilder()
            ->select('msg')
            ->from(Message::class, 'msg')
            ->where('msg.msg_forum = :forum')
            ->orderBy('msg.msg_id', 'desc')
            ->setParameter('forum', $forum)
            ->setMaxResults($db)
            ->getQuery()
            ->getResult());
    }

    public function getPage(Forum $forum, int $pageNum, int $numberPerPage = 10): array
    {
        $count = $this->count($forum);
        $fromNumber = ($pageNum-1) * $numberPerPage;
        if ($pageNum <= 0 || $fromNumber > $count) throw new NotFoundHttpException();

        return $this->em->createQueryBuilder()
            ->select('msg')
            ->from(Message::class, 'msg')
            ->where('msg.msg_forum = :forum')
            ->setParameter('forum', $forum)
            ->setFirstResult($fromNumber)
            ->setMaxResults($numberPerPage)
            ->getQuery()
            ->getResult();
    }

    public function isPagable(Forum $forum, int $currentPage = 1, int $numberPerPage = 10): ?array
    {
        $count = $this->count($forum);
        if ($count < $numberPerPage) return null;

        $upperPageLimit = (int) ceil(($count / $numberPerPage));

        $output = array();
        // 1, currentPage, upperLimit...
        if ($currentPage < 4) {
            $lowerLimit = 1;
        }
        else {
            $lowerLimit = $currentPage-3;
        }

        for ($i = $lowerLimit; $i < $currentPage; $i++) {
            $output []= $i;
        }
        for ($i = $currentPage; $i < $currentPage+3 && $i <= $upperPageLimit; $i++) {
            $output []= $i;
        }

        return $output;
    }

    public function isLastPage(Forum $forum, int $currentPage, int $numberPerPage = 10): bool
    {
        $count = $this->count($forum);
        if (!$count) return true;
        $upperPageLimit = (int) ceil(($count / $numberPerPage));
        return ($currentPage === $upperPageLimit);
    }

    public function lastPageNumber(Forum $forum, int $numberPerPage = 10): int
    {
        $count = $this->count($forum);
        $upperPageLimit = (int) ceil(($count / $numberPerPage));
        return $upperPageLimit;
    }
}