<?php
namespace App\Controller;

use App\Entity\Forum;
use App\Entity\Message;
use App\Entity\Publication;
use App\Entity\User;
use App\Service\AdServiceInterface;
use App\Service\FMessageServiceInterface;
use App\Service\ForumServiceInterface;
use App\Service\MailerServiceInterface;
use App\Service\PublicationServiceInterface;
use App\Service\SubscriberServiceInterface;
use App\Service\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ApiController
 * @package App\Controller
 * @Route(path="/api/")
 */
class ApiController extends AbstractController {
    /**
     * @param Request $request
     * @return Response
     * @Route(path="get3pubs")
     */
    public function get3PubsAction(Request $request, PublicationServiceInterface $pubsService) :Response {
        $array = array();
        /** @var Publication $pub */
        foreach ($pubsService->getVisiblePublications(3) as $pub) {
            $array []= ['text'=>$pub->getPubName(), 'href'=>$this->generateUrl('app_pub', ['pubId'=>$pub->getPubId()])];
        }
        $array []= ['text'=>"(view all)", 'href'=>"/pubs"];
        return new JsonResponse($array);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(path="get3forums")
     */
    public function get3ForumsAction(Request $request, ForumServiceInterface $pubsService) :Response {
        $array = array();
        /** @var Forum $pub */
        foreach ($pubsService->getAllVisibleForums(3) as $pub) {
            $array []= ['text'=>$pub->getFName(), 'href'=>$this->generateUrl('app_forum', ['forumId'=>$pub->getFId()])];
        }
        $array []= ['text'=>"(view all)", 'href'=>"/pubs"];
        return new JsonResponse($array);
    }

    /**
     * @param Request $request
     * @param PublicationServiceInterface $pubsService
     * @return Response
     * @Route(path="pub/{pubId}", requirements={"pubId": "\d+"})
     */
    public function getPubByIdAction(Request $request, int $pubId, PublicationServiceInterface $pubsService): Response {
        $this->denyAccessUnlessGranted("ROLE_MOD");
        $pub = $pubsService->getPublicationById($pubId);

        $array = array(
            'image'=>$pub->getPubImage(),
            'text'=>$pub->getPubText()
        );

        return new JsonResponse($array);
    }

    /**
     * @param Request $request
     * @param int $adId
     * @param AdServiceInterface $adService
     * @return Response
     * @Route(path="hirdetes/{adId}", requirements={"adId": "\d+"})
     */
    public function getSeenMessage(Request $request, int $adId, AdServiceInterface $adService): Response {
        $this->denyAccessUnlessGranted("ROLE_MOD");
        $ad = $adService->seenAd($adId);
        $adRemainig = $adService->countUnwatchedAds();
        $billing = array(
            'city' => $ad->getBilling()->getPostCode()." ".$ad->getBilling()->getCity(),
            'address' => $ad->getBilling()->getAddress(),
            'taxnum' => $ad->getBilling()->getTaxNumber()
        );
        return new JsonResponse(['message'=>nl2br($ad->getAdMessage()), 'adsCnt'=>$adRemainig, 'billing' => $billing, 'azon' => $ad->getPublicId()]);
    }

    /**
     * @param Request $request
     * @param int $userId
     * @param UserServiceInterface $userService
     * @return Response
     * @Route(path="user/{userId}", requirements={"userId": "\d+"})
     */
    public function getUserData(Request $request, int $userId, UserServiceInterface $userService): Response {
        $this->denyAccessUnlessGranted("ROLE_MOD");
        $user = $userService->getUserById($userId);

        if ($user->getUAvatar()) $avatar = "/users/images/".$user->getUAvatar();
        else $avatar = "/users/images/no-avatar.png";

        $array = array(
            'image'=>$avatar,
            'lname'=>$user->getLastName(),
            'fname'=>$user->getFirstName(),
            'tel'=>$user->getTelephone(),
            'email'=>$user->getEmail(),
            'email_visible'=>$user->getEmailVisibile(),
            'description'=>$user->getDescription()
        );

        return new JsonResponse($array);
    }

    /**
     * @param Request $request
     * @param int $msgId
     * @param FMessageServiceInterface $msgService
     * @return Response
     * @Route(path="msg/del/{msgId}", requirements={"msgId": "\d+"})
     */
    public function delMsgData(Request $request, int $msgId, FMessageServiceInterface $msgService): Response {
        $msg = $msgService->getMessageById($msgId);
        if ($msg->getMsgAuthor()->getUsername() != $this->getUser()->getUsername())
            $this->denyAccessUnlessGranted("ROLE_MOD");

        $msgService->delMessage($msgId);
        return new JsonResponse(['deleted'=>true]);
    }

    /**
     * @param Request $request
     * @param int $fId
     * @param FMessageServiceInterface $msgService
     * @param ForumServiceInterface $forumService
     * @return Response
     * @Route(path="msg/rfs/{fId}", requirements={"fId": "\d+"})
     */
    public function rfsMsgData(Request $request, int $fId, FMessageServiceInterface $msgService, ForumServiceInterface $forumService): Response {
        $forum = $forumService->getForumById($fId);

        $array = array(
            'lastId' => $msgService->lastId($forum),
            'cnt' => $msgService->count($forum)
        );

        return new JsonResponse($array);
    }

    /**
     * @param Request $request
     * @param int $fId
     * @param int $db
     * @param FMessageServiceInterface $msgService
     * @param ForumServiceInterface $forumService
     * @return Response
     * @Route(path="msg/get/{fId}/{db}", requirements={"fId": "\d+", "fId": "\d+"})
     */
    public function getNewMsgData(Request $request, int $fId, int $db, FMessageServiceInterface $msgService, ForumServiceInterface $forumService): Response {
        $forum = $forumService->getForumById($fId);

        $msgs = $msgService->getLastXMessages($forum, $db);
        $array = array();
        foreach ($msgs as &$msg) {
            $array [] = array(
                'id' => $msg->getMsgId(),
                'author' => $msg->getMsgAuthor()->getUsername(),
                'text' => $msg->getMsgText(),
                'date' => $msg->getMsgCreated()->format('Y M. d. H:i:s'),
                'editable' => $this->getUser() && ($msg->getMsgAuthor()->getUsername() == $this->getUser()->getUsername() || $this->isGranted('ROLE_MOD'))
            );
        }

        return new JsonResponse(['msgs'=>$array]);
    }

    /**
     * @param Request $request
     * @param int $fId
     * @param FMessageServiceInterface $msgService
     * @return Response
     * @Route(path="msg/add/{fId}", requirements={"fId": "\d+"})
     */
    public function addNewMsgData(Request $request, int $fId, FMessageServiceInterface $msgService, LoggerInterface $logger): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $text = $request->request->get('msg');

        $logger->info("msg text to save: ".$text);
        if (!$text) throw new NotFoundHttpException();
        $msg = $msgService->addMessage($this->getUser(), $text, $fId);

        $array = array(
            'id' => $msg->getMsgId(),
            'author' => $msg->getMsgAuthor()->getUsername(),
            'text' => $msg->getMsgText(),
            'date' => $msg->getMsgCreated()->format('Y M. d. H:i:s')
        );

        return new JsonResponse($array);
    }

    /**
     * @param Request $request
     * @param int $forumId
     * @param int $pageNum
     * @param ForumServiceInterface $forumService
     * @param FMessageServiceInterface $messageService
     * @return Response
     * @Route(path="msg/getpage/{forumId}/{pageNum}", requirements={"forumId": "\d+", "pageNum": "\d+"})
     */
    public function getFMessagesApiAction(Request $request, int $forumId, int $pageNum, ForumServiceInterface $forumService, FMessageServiceInterface $messageService): Response {
        $forum = $forumService->getForumById($forumId);

        $pageNums = $messageService->isPagable($forum, $pageNum);
        if ($pageNums) {
            $msgs = $messageService->getPage($forum, $pageNum); // numberPerPage) = 20 ...
        }
        else {
            $msgs = $messageService->getForumMessagesByForumId($forumId);
            $pageNum = 1;
        }

        $array = array();
        foreach ($msgs as &$msg) {
            $array [] = array(
                'id' => $msg->getMsgId(),
                'author' => $msg->getMsgAuthor()->getUsername(),
                'text' => $msg->getMsgText(),
                'date' => $msg->getMsgCreated()->format('Y M. d. H:i:s')
            );
        }

        return new JsonResponse(['msgs'=>$array, 'pages'=>$pageNums, 'currentPage'=>$pageNum]);
    }

    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return Response
     * @Route(name="app_subscribe", path="feliratkozas")
     */
    public function subscribeApiAction(Request $request, ValidatorInterface $validator, SubscriberServiceInterface $subscriberService, MailerServiceInterface $mailerService): Response {
        $email = $request->request->get('email');
        $emailConstraint = new Assert\Email();
        // all constraint "options" can be set this way
        $emailConstraint->message = 'Invalid email address';

        // use the validator to validate the value
        $errors = $validator->validate(
            $email,
            $emailConstraint
        );
        if (0 !== count($errors)) throw new \Exception("Email is invalid!");

        try {
            $subscriber =  $subscriberService->subscribe($email);
            $mailerService->sendHirdetEmail($email, 'email/hirlevel-base.html.twig', ['msg'=>"Ön sikeresen feliratkozott hírlevelünkre!", 'user'=>$this->getUser(), 'token'=>$subscriber->getToken()]);
        }
        catch (\Exception $exception) {return new Response($exception->getMessage());}
        return new Response("Sikeres hírlevél feliratkozás: ".$email);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="app_unsubscribe", path="leiratkozas")
     */
    public function unsubscribeApiAction(Request $request, SubscriberServiceInterface $subscriberService): Response {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')){
            $email = $this->getUser()->getEmail();
            $subscriberService->unsubscribe($email);
        }
        else {
            $token = $request->query->get('token');

            try {
                $email = $subscriberService->unsubscribeByToken($token);
            }
            catch (\Exception $exception) {return new Response($exception->getMessage());}
        }

        return new Response("Sikeres leiratkozás: ".$email);
    }

    /**
     * @param Request $request
     * @param UserServiceInterface $userService
     * @return Response
     * @Route(name="app_subhide", path="hidesub")
     */
    public function hideSubAction(Request $request, UserServiceInterface $userService): Response {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')){
            /** @var User $user */
            $user = $this->getUser();
            $user->setHideSubpanel(true);

            $userService->addUser($user);
        }
        else {
            $this->get('session')->set('hirlevel', true);
        }

        return new Response("OK.");
    }
}