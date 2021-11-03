<?php
namespace App\Controller;

use App\DTO\ForumDto;
use App\DTO\MessageDto;
use App\DTO\PublicationDto;
use App\Entity\Forum;
use App\Entity\Publication;
use App\Service\FMessageServiceInterface;
use App\Service\ForumServiceInterface;
use App\Service\PublicationServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MiujsagController
 * @Route(path="")
 */
class PubsController extends AbstractController {
    /** @var PublicationServiceInterface */
    private $pubsService;

    /**
     * MiujsagController constructor.
     * @param PublicationServiceInterface $pubsService
     */
    public function __construct(PublicationServiceInterface $pubsService)
    {
        $this->pubsService = $pubsService;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="app_main", path="/")
     * @Route(name="app_main_i", path="/index")
     * @Route(name="app_msg", path="/")
     */
    public function getMainAction(Request $request) : Response {
        $msg = $request->query->get('msg');
        if ($msg) {
            return $this->render('miujsag/msg.html.twig', ['msg' => $msg]);
        }
        $pubs = $this->pubsService->getVisiblePublications(2);
        return $this->render('miujsag/miujsag_base.html.twig', ['pubs'=>$pubs, 'noHeader'=>true, 'hirlevel'=>true]);
    }

    /**
     * @param Request $request
     * @param int $pubId
     * @return Response
     * @Route(name="app_pub", path="/megjelenesek/{pubId}", requirements={"pubId": "\d+"})
     */
    public function getPubsAction(Request $request, int $pubId = 0) : Response {
        if ($pubId) {
            return $this->redirect($this->pubsService->getPublicationRoute($pubId));
        }
        else {
            $moduser = $this->isGranted('ROLE_MOD');
            if ($moduser)
                $pubs = $this->pubsService->getAllPublications();
            else
                $pubs = $this->pubsService->getVisiblePublications();

            return $this->render('miujsag/pubs.html.twig', [ 'pubs' => $pubs, 'canPublicate' => $moduser ]);
        }
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="app_addpub", path="/megjelenesek/add/{pubId}", requirements={"pubId": "\d+"})
     */
    public function addPubsAction(Request $request, int $pubId = 0) : Response {
        $this->denyAccessUnlessGranted("ROLE_MOD");
        if ($pubId) $dto = new PublicationDto($this->pubsService->getPublicationById($pubId));
        else $dto = new PublicationDto();
        $form = $this->pubsService->getForm($dto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->pubsService->addPublication($dto, $this->getUser());
            return $this->redirectToRoute('app_pub');
        }

        return $this->render('miujsag/addpub.html.twig', [ 'form' => $form->createView() ]);
    }

    /**
     * @param Request $request
     * @param int $pubId
     * @param string $route
     * @return Response
     * @Route(name="app_delpub", path="/megjelenesek/del/{pubId}/{route}", requirements={"pubId": "\d+"})
     */
    public function delPubAction(Request $request, int $pubId, string $route) {
        $this->denyAccessUnlessGranted("ROLE_MOD");

        $this->pubsService->delPublication($pubId);

        return $this->redirectToRoute($route);
    }

    /**
     * @param Request $request
     * @param int $pubId
     * @return Response
     * @Route(name="app_editpub", path="/megjelenesek/edit/{pubId}/{route}", requirements={"pubId": "\d+"})
     */
    public function editPubAction(Request $request, int $pubId, string $route): Response {
        $this->denyAccessUnlessGranted("ROLE_MOD");
        $pub = $this->pubsService->getPublicationById($pubId);
        $form = $this->pubsService->getEditorForm($pub);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->pubsService->updatePublication($pub);
            return $this->redirectToRoute($route);
        }

        return $this->render('miujsag/addpub.html.twig', [ 'form' => $form->createView() ]);

    }
}