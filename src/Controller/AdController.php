<?php


namespace App\Controller;

use App\DTO\AdvertisementDto;
use App\Service\AdServiceInterface;
use App\Service\MailerServiceInterface;
use finfo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdController
 * @package App\Controller
 * @Route(path="/hirdet/")
 */
class AdController extends AbstractController {
    /** @var AdServiceInterface */
    private $adService;

    /**
     * AdController constructor.
     * @param AdServiceInterface $adService
     */
    public function __construct(AdServiceInterface $adService)
    {
        $this->adService = $adService;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="app_ad", path="")
     */
    public function createAdAction(Request $request, MailerServiceInterface $mailerService): Response {
        $ad_token = $request->getSession()->get('ad_token');
        if (!$ad_token) {
            $ad_token = sha1(random_bytes(50));
            $request->getSession()->set('ad_token', $ad_token);
        }

        $ad = new AdvertisementDto($this->getUser(), $ad_token);
        $form = $this->adService->getForm($ad);

        try{
            $form->handleRequest($request);
        }
        catch (\Exception $exception) {
            $this->addFlash('notice', "Kérem minden mezőt rendeltetésszerűen töltsön ki!");
            return $this->render('miujsag/ads.html.twig', ['form'=>$form->createView(), 'ad_token'=>$ad_token]);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            if ($ad->isNeedBilldata()) {
                $forgottenData = $request->request->get('form');
                $ad->setTaxNumber($forgottenData['tax_number']);
                $ad->setPostcode($forgottenData['postcode']);
                $ad->setCity($forgottenData['city']);
                $ad->setAddress($forgottenData['address']);
            }

            $newAd = $this->adService->addAd($ad);
            $mailerService->sendAdSubmission($newAd);
            return $this->redirectToRoute('app_msg', ['msg'=>"Sikeres hirdetésfeladás, nemsokára keresni fogjuk a megadott email címen: ".$ad->getAdEmail()]);
        }

        return $this->render('miujsag/ads.html.twig', ['form'=>$form->createView(), 'ad_token'=>$ad_token]);
    }

    /**
     * @param Request $request
     * @param int $adId
     * @return Response
     * @Route(name="app_viewad", path="{adId}", requirements={"adId": "\d+"})
     */
    public function getAdViewAction(Request $request, int $adId): Response {
        $this->denyAccessUnlessGranted("ROLE_MOD");
        return $this->render('miujsag/ads.html.twig', ['form'=>null, 'msg'=>"View Ads: ".$adId]);
    }

    /**
     * @param Request $request
     * @param string $fileName
     * @return Response
     * @Route(name="app_adlinks", path="getfile/{fileName}")
     */
    public function getAdFile(Request $request, string $fileName): Response {
        $this->denyAccessUnlessGranted("ROLE_MOD");

        $response = new Response(file_get_contents("../adv_files/".$fileName));
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
        $response->headers->set('Content-length', filesize("../adv_files/".$fileName));

        return $response;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="app_ad_fileupload", path="api/fileupload")
     */
    public function rawFilesApiAction(Request $request): Response {
        $token = $request->query->get('token');
        $output = array('uploaded' => false);
        // get the file from the request object
        $file = $request->files->get('file');
        // generate a new filename (safer, better approach)
        // To use original filename, $fileName = $this->file->getClientOriginalName();
        // $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $fileName = $file->getClientOriginalName();
        if (str_contains($fileName, ".php")) throw new \Exception('Invalid file format.');
        $ext = explode('.', $fileName);
        $ext = $ext[count($ext) - 1];
        if (false === in_array(
            $ext,
            array(
                "zip", "jpeg", "jpg", "png",
                "pdf", "doc", "docx",
                "xls", "xlsx", "csv", "tsv",
                "ppt", "pptx", "pages", "odt",
                "rtf"
            ))) {
            throw new \Exception('Invalid file format.');
        }

        // Note: While using $file->guessExtension(), sometimes the MIME-guesser may fail silently for improperly encoded files. It is recommended to use a fallback for such cases if you know what file extensions are expected. (You can loop-over the allowed file extensions or even hard-code it if you expect only a particular type of file extension.)

        // set your uploads directory
        $uploadDir = "../temp/$token";
        if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }
        if ($file->move($uploadDir, $fileName)) {
            $output['uploaded'] = true;
            $output['fileName'] = $fileName;
        }
        return new JsonResponse($output);
    }
}