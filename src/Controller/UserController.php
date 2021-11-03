<?php
namespace App\Controller;

use App\DTO\PwChangeDto;
use App\DTO\UserImageDto;
use App\Service\UserServiceInterface;
use App\Service\SecurityService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller
 * @Route(path="/user/")
 */
class UserController extends AbstractController {
    /** @var UserServiceInterface */
    private $userService;

    /**
     * UserController constructor.
     * @param UserServiceInterface $userService
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param Request $request
     * @param string $user
     * @return Response
     * @Route(name="app_user", path="{user}")
     */
    public function getUserDetailsAction(Request $request, string $user): Response {
        $user = $this->userService->getUser($user);

        return $this->render('user/user_data.html.twig', ['user'=>$user]);
    }

    /**
     * @param Request $request
     * @param string $user
     * @return Response
     * @Route(name="app_edituser", path="edit/{user}")
     */
    public function editUserAction(Request $request, string $user): Response {
        $user = $this->userService->getUser($user);
        if ($user->getUserId() != $this->getUser()->getUserId())
            $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $form = $this->userService->getForm($user, $this->isGranted("ROLE_ADMIN"));
        $userImage = new UserImageDto();

        $imgform = $this->userService->getPfpForm($userImage);
        $imgform->handleRequest($request);
        if ($imgform->isSubmitted() && $imgform->isValid()) {
            $user->setUAvatar($userImage->getUAvatarSaveChanges($user));
            $this->userService->editUser($user, $this->getUser());
        }
        else {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->userService->editUser($user, $this->getUser());
                return $this->redirectToRoute('app_user', ['user'=>$user->getUsername()]);
            }
        }

        return $this->render('user/user_edit.html.twig', ['form'=>$form->createView(), 'imgform'=>$imgform->createView(), 'user'=>$user]);
    }

    /**
     * @param Request $request
     * @param int $userId
     * @return Response
     * @Route(name="app_deluser", path="del/{userId}")
     */
    public function deleteUserAction(Request $request, int $userId): Response {
        if ($this->getUser()->getUserId() == $userId) {
            $session = $this->get('session');
            $session = new Session();
            $session->invalidate();
            $this->userService->delUser($userId);
            return $this->redirectToRoute('app_main');
        }
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        $this->userService->delUser($userId);
        return $this->redirectToRoute('app_admin_users');
    }

    /**
     * @param Request $request
     * @param int $userId
     * @return Response
     * @Route(name="app_userpass", path="passch/{userId}")
     */
    public function changePasswordAction(Request $request, int $userId, FormFactoryInterface $formFactory, SecurityService $security): Response {
        $user = $this->userService->getUserById($userId);
        if ($user->getUserId() != $this->getUser()->getUserId())
            $this->denyAccessUnlessGranted("ROLE_ADMIN");
        $userError = null;

        $dto = new PwChangeDto();
        $form = $dto->getForm($formFactory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
           if ($security->checkPassword($user, $dto->getOldPassword())) {
               $security->setUserPassword($user, $dto->getNewPassword());
               $this->addFlash('notice', "Jelszava sikeresen megváltozott!");
               return $this->redirectToRoute('app_user', ['user'=>$user->getUsername()]);
           }
           else $userError = "Rossz jelszót adott meg!";
        }

        return $this->render('security/pwreset.html.twig', ['form'=>$form->createView(), 'userError'=>$userError]);
    }
}