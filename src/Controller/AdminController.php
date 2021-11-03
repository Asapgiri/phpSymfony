<?php
namespace App\Controller;

use App\DTO\TempFileDto;
use App\Entity\Subscriber;
use App\Service\AdServiceInterface;
use App\Service\ForumServiceInterface;
use App\Service\MailerServiceInterface;
use App\Service\PublicationServiceInterface;
use App\Service\SubscriberServiceInterface;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @package App\Controller
 * @Route(path="/admin/")
 */
class AdminController extends AbstractController {
    /**
     * @param Request $request
     * @return Response
     * @Route(path="", name="app_admin")
     */
    public function getAdminPageAction(Request $request) : Response {
        $this->denyAccessUnlessGranted("ROLE_MOD");
        return $this->render('admin/adminpagebase.html.twig');
    }

    /**
     * @param Request $request
     * @param UserServiceInterface $userService
     * @return Response
     * @Route(path="users", name="app_admin_users")
     */
    public function getUsers(Request $request, UserServiceInterface $userService) {
        $this->denyAccessUnlessGranted("ROLE_MOD");
        $users = $userService->getAllUsers();
        return $this->render('admin/users.html.twig', ['users'=>$users]);
    }

    /**
     * @param Request $request
     * @param ForumServiceInterface $forumService
     * @Route(path="forums", name="app_admin_forums")
     */
    public function getForums(Request $request, ForumServiceInterface $forumService) {
        $this->denyAccessUnlessGranted("ROLE_MOD");
        $forums = $forumService->getAllForums();
        return $this->render('admin/forums.html.twig', ['forums'=>$forums]);
    }

    /**
     * @param Request $request
     * @param PublicationServiceInterface $publicationService
     * @Route(path="pubs", name="app_admin_pubs")
     */
    public function getPubs(Request $request, PublicationServiceInterface $publicationService) {
        $this->denyAccessUnlessGranted("ROLE_MOD");
        $pubs = $publicationService->getAllPublications();
        return $this->render('admin/pubs.html.twig', ['pubs'=>$pubs]);
    }

    /**
     * @param Request $request
     * @param AdServiceInterface $adService
     * @Route(path="ads", name="app_admin_ads")
     */
    public function getAdRequests(Request $request, AdServiceInterface $adService) {
        $this->denyAccessUnlessGranted("ROLE_MOD");
        $ads = $adService->getAll();
        return $this->render('admin/ads.html.twig', ['ads'=>$ads]);
    }

    /**
     * @param Request $request
     * @param SubscriberServiceInterface $subscriberService
     * @Route(path="subs", name="app_admin_subs")
     */
    public function getSubs(Request $request, SubscriberServiceInterface $subscriberService) {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $subs = $subscriberService->getSubscribers();
        if (!$subs) return new Response("Nem található feliratkozó!");

        $emails = implode(PHP_EOL, $subs);
        $response = new Response($emails);

        $date = (new \DateTime())->format('ymd_His_');
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $date.'hirlevel.txt'
        );

        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }

    /**
     * @param Request $request
     * @param SubscriberServiceInterface $subscriberService
     * @Route(path="subs_send", name="app_admin_subs_send")
     */
    public function sendHtmlToSubs(Request $request, SubscriberServiceInterface $subscriberService, MailerServiceInterface $mailerService) {
        $this->denyAccessUnlessGranted("ROLE_MOD");
        $msg = $request->query->get('msg');
        if ($msg) $this->addFlash('notice', $msg);

        $dto = new TempFileDto();
        $form = $subscriberService->getForm($dto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $subs = $subscriberService->getSubscribers();
            if (!$subs) return $this->redirectToRoute('app_admin_subs_send', ['msg'=>"Nem található feliratkozó!"]);

            $file = $dto->getTempFile();
            $fileName = $file->getClientOriginalName();
            var_dump($fileName);
            if (str_contains($fileName, ".php")) throw new \Exception('Invalid file format.');
            $ext = explode('.', $fileName);
            $ext = $ext[count($ext) - 1];
            /*if (false === in_array(
                    $ext,
                    array(
                        "twig", "html", "txt"
                    ))) {
                return $this->redirectToRoute('app_admin_subs_send', ['msg'=>"Nem megengedett fájlformátum!"]);
            }*/
            if ($ext == "twig") return $this->redirectToRoute('app_admin_subs_send', ['msg'=>"A formátum még nem támogatott!"]);
            $fileContent = file_get_contents($file);
            $fileContent = mb_convert_encoding($fileContent, 'UTF-8', mb_detect_encoding($fileContent, 'UTF-8, ISO-8859-1', true));
            $failcnt = 0;

            if ($dto->isIsTest()) {
                if ($ext == "html" || $ext == "htm" || $ext == "txt") {
                    $mailerService->sendHirdetEmail("", 'email/hirlevel-base.html.twig', ['msg'=>$fileContent, 'token'=>null], true, $dto->getSubject());
                }
                else {
                    return $this->redirectToRoute('app_admin_subs_send', ['msg'=>"Helytelen formátum!"]);
                }
            }
            else {
                if ($ext == "html" || $ext == "htm" || $ext == "txt") {
                    /** @var Subscriber $sub */
                    foreach ($subs as $sub) {
                        try {
                            $mailerService->sendHirdetEmail($sub->getEmail(), 'email/hirlevel-base.html.twig', ['msg'=>$fileContent, 'token'=>$sub->getToken()], false, $dto->getSubject());
                        }
                        catch (Exception $e) {
                            $failcnt++;
                        }
                    }
                }
                else {
                    return $this->redirectToRoute('app_admin_subs_send', ['msg'=>"Helytelen formátum!"]);
                }
            }

            $db = count($subs);
            return $this->redirectToRoute('app_admin_subs_send', ['msg'=>"Sikeres kiküldés: $db db feliratkozónak! Sikertelen: $failcnt db."]);
        }

        return $this->render('admin/subs.html.twig', ['form'=>$form->createView()]);
    }
}