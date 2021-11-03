<?php
namespace App\Service;

use App\DTO\ForumDto;
use App\Entity\Forum;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\Length;

class ForumService extends CrudService implements ForumServiceInterface {
    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory)
    {
        parent::__construct($em, $formFactory);
    }

    public function getRepo(): EntityRepository
    {
        return $this->em->getRepository(Forum::class);
    }

    public function getAllForums(): iterable
    {
        return $this->getRepo()->findBy(array(), ['f_created'=>"desc"]);
    }

    public function getForumById(int $forumId): Forum
    {
        return $this->getRepo()->findOneBy(['f_id'=>$forumId]);
    }

    public function getForumsByUser(int $userId): iterable
    {
        return $this->getRepo()->findBy(['f_author'=>$userId]);
    }

    public function getAllVisibleForums(int $numDb = 0): iterable
    {
        if ($numDb) {
            $qb = $this->em->createQueryBuilder();
            $qb->select('forum')
                ->from(Forum::class, 'forum')
                ->orderBy('forum.f_created', 'desc')
                ->where("forum.f_visible = true")
                ->setMaxResults($numDb);

            $query = $qb->getQuery();
            return $query->getResult();
        }
        else return $this->getRepo()->findBy(['f_visible'=>true], ['f_lastmsg'=>"desc"]);
    }

    public function addForum(User $user, ForumDto $dto): bool
    {
        $forum = new Forum();
        $forum->setFVisible($dto->isForumVisibility());
        $forum->setFName($dto->getForumName());
        $forum->setFDescription($dto->getDescription());
        $forum->setFAuthor($user);
        $this->em->persist($forum);
        $this->em->flush();
        return true;
    }

    public function delForum(Forum $forum): bool
    {
        $messages = $this->em->getRepository(Message::class)->findBy(['msg_forum'=>$forum]);
        foreach ($messages as $msg) {
            $this->em->remove($msg);
        }
        $this->em->remove($forum);
        $this->em->flush();
        return true;
    }

    public function getForm(ForumDto $oneForum, bool $canAddInvis = false): FormInterface
    {
        $form = $this->formFactory->createBuilder(FormType::class, $oneForum);
        $form->add('forum_name', TextType::class, [ 'required' => true, 'label' => false, 'row_attr'=>['class'=>"col-10 pr-1"],
            'attr' => ['placeholder' => "Új fórum neve", 'maxlength' => "255"]
        ]);
        $form->add('Létrehozás', SubmitType::class, ['row_attr'=>['class'=>"col-2 pl-1"], 'attr' => ['class'=>"btn-primary btn-block", 'style'=>"height: 100%;"]]);
        $form->add('description', TextareaType::class, [ 'required' => false, 'label' => false, 'row_attr' => [ 'class' => "col-10 pr-1" ],
            'attr' => [
                'oninput' => "if (this.scrollHeight < 150) {this.style.height = ``;this.style.height = this.scrollHeight + 3 + `px`}",
                'placeholder' => "Leírás",
                'maxlength' => "1000"
            ],
            'constraints' => [
                new Length([
                    'max' => 1000,
                    'maxMessage' => "Az leírás maximális hosszúsága {{ limit }} karakter!"
                ])
            ]
        ]);
        if ($canAddInvis) $form->add('forum_visibility', CheckboxType::class, [ 'row_attr'=>['class'=>"col-2 pl-10"], 'label' => "Látható", 'value' => true, 'required' => false ]);
        return $form->getForm();
    }

    public function updateForum(Forum $forum): void
    {
        $this->em->persist($forum);
        $this->em->flush();
    }

    public function getPage(bool $admin, int $pageNum, int $numberPerPage = 10): array
    {
        $count = $this->count($admin);
        $fromNumber = ($pageNum-1) * $numberPerPage;
        if ($pageNum <= 0 || $fromNumber > $count) throw new NotFoundHttpException();

        $query = $this->em->createQueryBuilder()
            ->select('forum')
            ->from(Forum::class, 'forum')
            ->orderBy('forum.f_created', 'desc');
        if (!$admin) $query->where('forum.f_visible = true');

        return $query->setFirstResult($fromNumber)
            ->setMaxResults($numberPerPage)
            ->getQuery()
            ->getResult();
    }

    public function isPagable(bool $admin, int $currentPage = 1, int $numberPerPage = 10): ?array
    {
        $count = $this->count($admin);
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

    public function isLastPage(bool $admin, int $currentPage, int $numberPerPage = 10): bool
    {
        $count = $this->count($admin);
        $upperPageLimit = (int) ceil(($count / $numberPerPage));
        return ($currentPage === $upperPageLimit);
    }

    public function lastPageNumber(bool $admin, int $numberPerPage = 10): int
    {
        $count = $this->count($admin);
        $upperPageLimit = (int) ceil(($count / $numberPerPage));
        return $upperPageLimit;
    }

    public function count(bool $admin = true): int
    {
        $query = $this->em->createQueryBuilder()
            ->select('count(forum.f_id)')
            ->from(Forum::class, 'forum');
        if (!$admin) $query->where('forum.f_visible = true');

        return $query->getQuery()
            ->getSingleScalarResult();
    }
}