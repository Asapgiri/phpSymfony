<?php
namespace App\Controller;

use App\DTO\ForumDto;
use App\DTO\MessageDto;
use App\Entity\User;
use App\Service\FMessageServiceInterface;
use App\Service\ForumServiceInterface;
use App\Service\PublicationServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ForumController
 * @package App\Controller
 * @Route(path="/forum/")
 */
class ForumController extends AbstractController {
    /** @var ForumServiceInterface */
    private $forumService;

    /** @var FMessageServiceInterface */
    private $f_messagesService;

    /**
     * MiujsagController constructor.
     * @param ForumServiceInterface $forumService
     * @param FMessageServiceInterface $f_messagesService
     * @param PublicationServiceInterface $pubsService
     */
    public function __construct(ForumServiceInterface $forumService, FMessageServiceInterface $f_messagesService)
    {
        $this->forumService = $forumService;
        $this->f_messagesService = $f_messagesService;
    }

    /**
     * @param Request $request
     * @param int $forumId
     * @return Response
     * @Route(name="app_forum", path="{forumId}", requirements={"forumId": "\d+"})
     */
    public function getForumAction(Request $request, int $forumId) : Response {
        $page = $request->query->getInt('page');
        if (!$page) $page = 1;
        $forum = $this->forumService->getForumById($forumId);
        $messages = $this->f_messagesService->getPage($forum, $page);

        /*if ($this->getUser() && $this->f_messagesService->isLastPage($forum, $page)) {
            $dto = new MessageDto();
            $form = $this->f_messagesService->getForm($dto);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->f_messagesService->addMessage($this->getUser(), $dto->getMsgText(), $forumId);
                return $this->redirectToRoute('app_forum', ['forumId'=>$forumId]);
            }
            $form = $form->createView();
        }
        else $form = null;*/

        return $this->render('miujsag/fmessages.html.twig', [
            'form' => $this->getUser() && $this->f_messagesService->isLastPage($forum, $page),
            'messages' => $messages,
            'forum' => $forum,
            'pageable' => $this->f_messagesService->isPagable($forum),
            'current' => $page,
            'last' => $this->f_messagesService->lastPageNumber($forum)
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(path="", name="app_forums")
     */
    public function getForumsAction(Request $request): Response {
        $page = $request->query->getInt('page');
        if (!$page) $page = 1;
        $moduser = $this->isGranted('ROLE_MOD');

        /** @var User $user */
        $user = $this->getUser();

        if ($user) {
            $dto = new ForumDto();
            $form = $this->forumService->getForm(
                $dto,
                $this->isGranted('ROLE_MOD')
            );

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->forumService->addForum($user, $dto);
                $userForums = $this->forumService->getForumsByUser($user->getUserId());
                return $this->redirectToRoute('app_forum', ['forumId'=>$userForums[count($userForums)-1]->getFId()]);
            }

            $form = $form->createView();
        }
        else $form = null;

        return $this->render('miujsag/forum.html.twig', [
            'form' => $form,
            'forums' => $this->forumService->getPage($moduser, $page),
            'pageable' => $this->forumService->isPagable($moduser),
            'current' => $page,
            'last' => $this->forumService->lastPageNumber($moduser)
        ]);

    }

    /**
     * @param Request $request
     * @param int $forumId
     * @return Response
     * @Route(name="app_delforum", path="del/{forumId}", requirements={"forumId": "\d+"})
     */
    public function getDeleteForumAction(Request $request, int $forumId) : Response {
        $forum = $this->forumService->getForumById($forumId);
        if ($this->getUser()->getUserId() != $forum->getFAuthor()->getUserId())
            $this->denyAccessUnlessGranted("ROLE_MOD");

        $this->forumService->delForum($forum);

        return $this->redirectToRoute('app_forums');
    }

    /**
     * @param Request $request
     * @param string $visible
     * @return Response
     * @Route(name="app_chforumvis", path="vis/{forumId}/{visible}", requirements={"forumId": "\d+"})
     */
    public function changeForumVisibleAction(Request $request, int $forumId, string $visible): Response {
        $this->denyAccessUnlessGranted("ROLE_MOD");
        $forum = $this->forumService->getForumById($forumId);
        if ($forum) {
            if ($visible === "true") {
                $forum->setFVisible(true);
            }
            else if ($visible === "false") {
                $forum->setFVisible(false);
            }
            else {
                throw new NotFoundHttpException();
            }
            $this->forumService->updateForum($forum);
            return $this->redirectToRoute('app_forum', ['forumId'=>$forumId]);
        }
        throw new NotFoundHttpException();
    }
}