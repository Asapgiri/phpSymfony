<?php
namespace App\Controller;

use App\DTO\LoginDto;
use App\DTO\PwResetDto;
use App\DTO\PwResetRequestDto;
use App\DTO\RegistrationDto;
use App\Entity\User;
use App\Service\PwResetServiceInterface;
use App\Service\UserServiceInterface;
use App\Service\MailerServiceInterface;
use App\Service\SecurityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SecurityController extends AbstractController {
    /** @var SecurityService */
    private $security;
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var TokenStorageInterface */
    private $tokenStorage;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * SecurityController constructor.
     * @param SecurityService $security
     * @param FormFactoryInterface $formFactory
     * @param TokenStorageInterface $tokenStorage
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(SecurityService $security, FormFactoryInterface $formFactory, TokenStorageInterface $tokenStorage, EventDispatcherInterface $eventDispatcher)
    {
        $this->security = $security;
        $this->formFactory = $formFactory;
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="app_register", path="/register")
     */
    public function registerAction(Request $request, MailerServiceInterface $mailerService) : Response {
        $dto = new RegistrationDto($this->formFactory, $request);
        $form = $dto->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $user = $this->security->registerUser($dto->getUserName(), $dto->getEmail(), $dto->getClearPass(), $dto->getFirstName(), $dto->getLastName());
            }
            catch (\Exception $exception) {
                $this->addFlash('notice', $exception->getMessage());
                return $this->render('security/register.html.twig', ['form'=>$form->createView()]);
            }
            if (!$user) {
                $this->addFlash('notice', "Regisztrációs hiba!");
                return $this->render('security/register.html.twig', ['form'=>$form->createView()]);
            }
            /** @var User $user */
            $mailerService->sendRegisterEmail($user);
            $this->loginUser($request, $user);
            $this->addFlash('notice', "{$dto->getUserName()} sikeres regisztráció!");
            return $this->redirectToRoute('app_main');
        }

        return $this->render('security/register.html.twig', ['form'=>$form->createView()]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="app_login", path="/login")
     */
    public function loginAction(Request $request, AuthenticationUtils $authenticationUtils) : Response {
        $dto = new LoginDto($this->formFactory, $request);
        $dto->setUserName($authenticationUtils->getLastUsername());
        $from = $request->request->get("from");

        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            if (!$from)
                return $this->redirectToRoute('app_main');
            else {
                $from = str_replace(".", "/", $from);
                return $this->redirect($from);
            }
        }

        return $this->render('security/login.html.twig', [
            'form' => $dto->getForm()->createView(),
            'myUser' => $this->getUser(),
            'authError' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="app_loginapi", path="/api/login")
     */
    public function loginApiAction(Request $request, AuthenticationUtils $authenticationUtils) : Response {
        $dto = new LoginDto($this->formFactory, $request);
        $dto->setUserName($authenticationUtils->getLastUsername());
        $from = $request->request->get("from");

        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new Response("You are already logged in!");
        }

        return $this->render('security/loginform.html.twig', [
            'form' => $dto->getForm()->createView(),
            'myUser' => $this->getUser(),
            'authError' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="app_beforelogout", path="/logoutb")
     */
    public function beforeLogoutAction(Request $request, UserServiceInterface $userService) : Response {
        /** @var User $user */
        $user = $this->getUser();
        $user->setUOnline(false);
        $userService->editUser($user, $this->getUser());
        return $this->redirectToRoute('app_logout');
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="app_logout", path="/logout")
     */
    public function logoutAction(Request $request) : Response {
        // Will be done by the framework...
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="protected_content", path="/protected")
     */
    public function protectedAction(Request $request) : Response {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        return new Response("Some top secret data...");
    }

    private function loginUser(Request $request, UserInterface $user) : void
    {
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->tokenStorage->setToken($token);

        $event = new InteractiveLoginEvent($request, $token);
        $this->eventDispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $event);
    }

    /**
     * @param Request $request
     * @param PwResetServiceInterface $pwResetService
     * @param FormFactoryInterface $formFactory
     * @param UserServiceInterface $userService
     * @return Response
     * @Route(name="request_password_reset", path="/pwr_r")
     */
    public function requestPasswordResetAction(Request $request, PwResetServiceInterface $pwResetService, FormFactoryInterface $formFactory, UserServiceInterface $userService, MailerServiceInterface $mailerService): Response {
        $dto = new PwResetRequestDto();
        $form = $dto->getForm($formFactory);
        $userError = null;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userService->getUserByEmail($dto->getEmail());

            if (!$user) {
                $userError = "Ilyen emailcím nem található az oldalon!";
            }
            else {
                $newToken = $pwResetService->createToken($user);

                $mailerService->sendPwResetEmail($user, $newToken);

                $this->addFlash('notice', "Email sikeresen elküldve!");
                return $this->redirectToRoute('app_main');
            }
        }

        return $this->render('security/pwreset.html.twig', ['form'=>$form->createView(), 'userError' => $userError]);
    }

    /**
     * @param Request $request
     * @param PwResetServiceInterface $pwResetService
     * @return Response
     * @Route(name="reset_password", path="/pwr")
     */
    public function resetPasswordAction(Request $request, PwResetServiceInterface $pwResetService, UserPasswordEncoderInterface $encoder): Response {
        $pwToken = $request->query->get('token');
        $token = $pwResetService->validateUserToken($pwToken);
        if (!$token) throw new AccessDeniedException();
        $userError = null;
        $user = $token->getPwrUser();

        $dto = new PwResetDto();
        $form = $pwResetService->getForm($dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->security->setUserPassword($user, $dto->getNewPassword());
            $pwResetService->deleteToken($token);
            $this->addFlash('notice', "A jelszava sikeresen megváltozott!");
            $this->loginUser($request, $user);
            return $this->redirectToRoute('app_main');
        }

        return $this->render('security/pwreset.html.twig', ['form'=>$form->createView(), 'userError' => $userError, 'user'=>$user->getUsername()]);
    }
}