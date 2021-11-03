<?php
namespace App\Service;
// CORS = Cross Origin Resource Sharing

use http\Client\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Http\Firewall\LogoutListener;
use Twig\Environment;

class MyListenerService { // vs Subscribers ...
    /** @var ParameterBagInterface */
    private $parameterBag;
    /** @var Environment */
    private $twig;

    /**
     * MyListenerService constructor.
     * @param ParameterBagInterface $parameterBag
     * @param Environment $twig
     */
    public function __construct(ParameterBagInterface $parameterBag, Environment $twig)
    {
        $this->parameterBag = $parameterBag;
        $this->twig = $twig;
    }

    public function onKernelRequest(RequestEvent $event) {
        if (!$event->isMasterRequest()) return;
        $request = $event->getRequest();
        $method = $request->getRealMethod();
        if ($method == "OPTIONS") $event->setResponse(new Response(""));
        $this->twig->addGlobal('menu', $this->parameterBag->get("menu"));
    }

    public function onKernelResponse(ResponseEvent $event) {
        if (!$event->isMasterRequest()) return;
        $response = $event->getResponse();
        // TODO: Allow only custun only endpoints/origins to access my API...
        // Adding CORS response headers
        $response->headers->set("Access-Control-Allow-Origin", "*");
        $response->headers->set("Access-Control-Allow-Methods", "GET, POST, OPTIONS");
        $response->headers->set("Access-Control-Allow-Headers", "Content-Type, Authorization");
    }
}