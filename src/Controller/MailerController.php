<?php
namespace App\Controller;

use App\Entity\User;
use App\Service\MailerServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController {
    /**
     * @param MailerServiceInterface  $mailer
     * @return Response
     * @Route(path="/email")
     */
    public function firstMailTest(Request $request, MailerServiceInterface $mailerService): Response {
        /** @var User $user */
        $user = $this->getUser();
        $mailerService->sendRegisterEmail($user);

        return new Response("OK.");
    }
}