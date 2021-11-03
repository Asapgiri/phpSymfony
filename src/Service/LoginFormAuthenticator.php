<?php
namespace App\Service;

use App\DTO\LoginDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator {
    use TargetPathTrait; //For redirect

    /** @var EntityManagerInterface */
    private $em;
    /** @var RouterInterface */
    private $router;
    /** @var UserPasswordEncoderInterface */
    private $passwordComposer;
    /** @var FormFactoryInterface */
    private $formFactory;

    /**
     * LoginFormAuthenticator constructor.
     * @param EntityManagerInterface $em
     * @param RouterInterface $router
     * @param UserPasswordEncoderInterface $passwordComposer
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(EntityManagerInterface $em, RouterInterface $router, UserPasswordEncoderInterface $passwordComposer, FormFactoryInterface $formFactory)
    {
        $this->em = $em;
        $this->router = $router;
        $this->passwordComposer = $passwordComposer;
        $this->formFactory = $formFactory;
    }

    protected function getLoginUrl()
    {
        return $this->router->generate("app_login");
    }

    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'app_login' && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $dto = new LoginDto($this->formFactory, $request);
        $form = $dto->getForm();
        $form->handleRequest($request);
        if (!$form->isValid() || !$form->isSubmitted()) throw new InvalidCsrfTokenException("INVALID FORM");
        $request->getSession()->set(Security::LAST_USERNAME, $dto->getUserName());
        return $dto;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var LoginDto $credentials */
        $user = $this->em->getRepository(User::class)->findOneBy(['userName'=>$credentials->getUserName()]);
        if (!$user) throw new CustomUserMessageAuthenticationException("BAD USERNAME OR PASSWORD");
        // else throw new CustomUserMessageAuthenticationException("USERNAME: {$user->getUsername()}, PASSWORD: {$user->getUserPass()}");
        //$user->setUOnline(true);
        //$this->em->persist($user);
        //$this->em->flush();
        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user) {
        /** @var LoginDto $credentials */
        return $this->passwordComposer->isPasswordValid($user, $credentials->getUserPass());
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetPath = $this->getTargetPath($request->getSession(), $providerKey);
        if (!$targetPath) $targetPath = $this->router->generate("app_login");
        return new RedirectResponse($targetPath);
    }
}