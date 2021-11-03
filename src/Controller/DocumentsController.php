<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DocumentsController
 * @package App\Controller
 * @Route(path="/docs")
 */
class DocumentsController extends AbstractController {
    /**
     * @param Request $request
     * @return Response
     * @Route(name="app_aszf", path="/aszf")
     */
    public function aszfAction(Request $request): Response {
        return $this->render('docs/aszf.html.twig', ['noHeader'=>true]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="app_avsz", path="/avsz")
     */
    public function avszAction(Request $request): Response {
        return $this->render('docs/avsz.html.twig', ['noHeader'=>true]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="app_impresszum", path="/impresszum")
     */
    public function impresszumAction(Request $request): Response {
        return $this->render('docs/impresszum.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="app_conn", path="/kapcsolat")
     */
    public function connThisAction(Request $request): Response {
        return $this->render('docs/connection.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="app_conns", path="/kapcsolatok")
     */
    public function connectionsAction(Request $request): Response {
        return $this->render('docs/connections.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="app_info", path="/info")
     */
    public function infoAction(Request $request): Response {
        return $this->render('docs/info.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="app_dijak", path="/dijszabas")
     */
    public function dijakAction(Request $request): Response {
        return $this->render('docs/dijak.html.twig');
    }
}