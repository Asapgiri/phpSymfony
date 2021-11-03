<?php
namespace App\Service;

use App\Doctrine\AdIdGenerator;
use App\DTO\AdvertisementDto;
use App\Entity\AdUnit;
use App\Entity\Advertisement;
use App\Entity\BillingData;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdService extends CrudService implements AdServiceInterface {
    /** @var AdUnitServiceInterface */
    private $adUnitService;

    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory, AdUnitServiceInterface $adUnitService)
    {
        parent::__construct($em, $formFactory);
        $this->adUnitService = $adUnitService;
    }

    public function getRepo(): EntityRepository
    {
        return $this->em->getRepository(Advertisement::class);
    }

    public function getAll(): array
    {
        return $this->getRepo()->findBy([], ['ad_id'=>'desc']);
    }

    public function getAdById(int $adId): Advertisement
    {
        return $this->getRepo()->findOneBy(['ad_id'=>$adId]);
    }

    public function addAd(AdvertisementDto $dto): Advertisement
    {
        $ad = new Advertisement($dto);

        if ($dto->getAdZip()) {
            $date = date("Ymd_Hsi_");
            $tempstore = $dto->getAdToken();
            $zipName = $date.str_replace(' ', '_', $dto->getAdAname()).'.zip';
            $files = array_filter(explode(';', $dto->getAdZip()), fn($value) => !is_null($value) && $value !== '');

            //$root = $this->kernel->getProjectDir();
            $zip = new \ZipArchive();
            $zip->open("../adv_files/".$zipName, \ZipArchive::CREATE);
            foreach ($files as $file) {
                $zip->addFile("../temp/$tempstore/$file", $file);
            }
            $zip->close();

            /*$dto->getAdZip()->move(
                "../adv_files/",
                $date.$dto->getAdZip()->getClientOriginalName()
            );*/



            $ad->setAdZip($zipName);
            $ad->setAdFiles(implode(', ', $files));
        }
        if (is_dir('../temp/'.$dto->getAdToken())) {
            $tempstore = $dto->getAdToken();
            $files = glob('../temp/'.$tempstore.'/*', GLOB_MARK);
            foreach ($files as &$file) unlink($file);
            rmdir('../temp/'.$tempstore);
        }

        $author = $ad->getAdAuthor();
        if ($author && !$author->getTelephone() && $ad->getAdTelephone()) {
            $author->setTelephone($ad->getAdTelephone());
            $this->em->persist($author);
        }
        $ad->setPublicId((new AdIdGenerator())->generate($this->em, $ad));
        $this->em->persist($ad->getBilling());
        $this->em->persist($ad);
        $this->em->flush();
        return $ad;
    }

    public function delAd(int $adId): void
    {
        $ad = $this->getAdById($adId);
        $this->em->remove($ad);
        $this->em->flush();
    }

    public function getForm(AdvertisementDto $dto): FormInterface
    {
        $form = $this->formFactory->createBuilder(FormType::class, $dto, ['allow_extra_fields'=>true]);
        $form->add('ad_aname', TextType::class, [
            'required' => true,
            'label' => "Név / Cégnév",
            'invalid_message' => "Kérem adja meg hogyan nevezhetjük meg!",
            'invalid_message_parameters' => " ",
            'attr' => [
                'minlength' => "3",
                'maxlength' => "125",
            ]
        ]);

        $form->add('ad_email', EmailType::class, ['required'=>true, 'label'=>"Email cím", 'attr' => [
            'minlength' => "3",
            'maxlength' => "254",
        ]]);
        $form->add('telephone', TelType::class, ['required'=>false, 'label'=>"Telefon",
            'row_attr' => [
                'class' => "mb-5"
            ],
            'attr' => [
                'style' => "width: 250px;",
                'maxlength' => "15",
                'onkeyup' => "telCheck(event, this.value)",
            ]
        ]);

        // billing datas...

        $user = $dto->getAdAuthor();
        if ($user && !$user->getBilling()->isEmpty()) {
            $choices = array(''=>"");
            /** @var BillingData $billing */
            foreach ($user->getBilling() as $billing) {
                $choices[$billing->__toString()] = $billing;
            }

            $form->add('billing', ChoiceType::class, [
                'label' => "Számlázás",
                'choices' => $choices,
                'row_attr' => [
                    'id' => "tax-selector"
                ]
            ]);

            $form->add('need_billdata', HiddenType::class);
            $form->add('button', ButtonType::class, [
                'label' => "Új számlázási cím hozzáadása",
                'attr' => [
                    'onclick' => "addTaxFields()"
                ],
                'row_attr' => [
                    'id' => "tax-button"
                ]
            ]);
        }
        else {
            $form->add('tax_number', TextType::class, [
                'label' => "Adószám",
                'required' => false,
                'attr' => [
                    'minlength' => "13",
                    'maxlength' => "13",
                    'style' => "width: 250px;",
                    'onkeyup' => "adszCheck(event, this.value)",
                    'placeholder' => "________-_-__"
                ]
            ]);
            $form->add('postcode', TextType::class, [
                'label' => "Irányítószám",
                'attr' => [
                    'minlength' => "4",
                    'maxlength' => "4",
                    'style' => "width: 100px;",
                    'onkeyup' => "postCodeCheck(event, this.value)",
                    'placeholder' => "____"
                ]
            ]);
            $form->add('city', TextType::class, [
                'required' => true,
                'label' => "Város / Helység",
                'attr' => [
                    'maxlength' => "55",
                    'style' => "max-width: 500px;"
                ],
                'help' => "---"
            ]);
            $form->add('address', TextType::class, [
                'label' => "Utca, házszám",
                'required' => true,
                'attr' => [
                    'maxlength' => "550"
                ]
            ]);
        }

        // end billing datas...

        $form->add('ad_message', TextareaType::class, [
            'required' => true,
            'label' => "Üzenet és hirdetés szövege",
            'attr' => ['oninput' => "if (this.scrollHeight < 500) {this.style.height = ``;this.style.height = this.scrollHeight + 3 + `px`}", 'maxlength' => "10000"],
            'row_attr' => ['class' => "mt-5"],
            'constraints' => [
                new NotBlank(['message' => "Üres üzenetet nem küldhet el!"]),
                new Length([
                    'min' => 10,
                    'minMessage' => "Az üzenet minimum hósszúsága {{ limit }} karakter! Kérem töltse ki a mezőt.",
                    'max' => 10000,
                    'maxMessage' => "Az üzenet maximális hosszúsága {{ limit }} karakter! Ha ennél hosszabban szeretne fogalmazni kérem töltse fel file formátumban."
                ])
            ]
        ]);

        /*
        $form->add('ad_zip', FileType::class, [
            'required' => false, 'label' => "Ide tölthet fel filokat: (zip, jpeg, jpg, png, pdf, doc(x), xls(x))",
            'multiple' => true,
            'constraints'=>[
                new File([
                    'mimeTypes' => [
                        "application/zip",
                        "image/jpeg",
                        "image/jpg",
                        "image/png",
                        "application/pdf",
                        "application/doc",
                        "application/docx",
                        "application/xls",
                        "application/xlsx"
                    ],
                    'mimeTypesMessage' => "Kérem .zip formátumot töltsön fel."
                ])
            ]
        ]);*/
        $form->add('ad_token', HiddenType::class);
        $form->add('ad_zip', HiddenType::class);

        $adUnits = $this->adUnitService->getAll();
        $adTypes = [' '=>""];
        /** @var AdUnit $adUnit */
        foreach ($adUnits as $adUnit) {
            $adTypes[$adUnit->__toString()] = $adUnit->getUnitName();
        }

        $form->add('ad_type', ChoiceType::class, [
            'label' => "Hirdetés típusa",
            'required' => true,
            'choices' => $adTypes,
            'empty_data' => ""
        ]);

        // $form->add('Elküldés', SubmitType::class);
        return $form->getForm();
    }

    public function getUnwatchedAds(): array
    {
        return $this->getRepo()->findBy(['ad_watched'=>false], ['ad_datetime'=>"desc"]);
    }

    public function countUnwatchedAds(): int
    {
        return $this->getRepo()->count(['ad_watched'=>false]);
    }

    public function seenAd(int $adId): Advertisement
    {
        $ad = $this->getAdById($adId);
        $ad->setAdWatched(true);
        $this->em->persist($ad);
        $this->em->flush();
        return $ad;
    }
}