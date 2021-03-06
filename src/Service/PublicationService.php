<?php
namespace App\Service;

use App\DTO\PublicationDto;
use App\Entity\Publication;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PublicationService extends CrudService implements PublicationServiceInterface {
    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory)
    {
        parent::__construct($em, $formFactory);
    }

    public function getRepo(): EntityRepository
    {
        return $this->em->getRepository(Publication::class);
    }

    public function getAllPublications(): array
    {
        return $this->getRepo()->findBy(array(), ['pub_date'=>"desc"]);
    }

    public function getVisiblePublications(int $numDb = 0): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('pub')
            ->from(Publication::class, 'pub')
            ->orderBy('pub.pub_date', 'desc')
            ->where("pub.pub_visible = true")
            ->where("pub.pub_date < :nowdate")
            ->setParameter("nowdate", new \DateTime());

        if ($numDb) $qb->setMaxResults($numDb);
        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function getPublicationById(int $pubId): Publication
    {
        return $this->getRepo()->findOneBy(['pub_id'=>$pubId]);
    }

    public function getPublicationByUser(int $userId): array
    {
        return $this->getRepo()->findBy(['pub_author'=>$userId]);
    }

    public function addPublication(PublicationDto $dto, User $user): bool
    {
        $pub = new Publication();
        $pub->setPubVisible($dto->isPubVisible());
        $pub->setPubName($dto->getPubName());
        $pub->setPubText($dto->getPubText());
        $pub->setPubAuthor($user);
        $pub->setPubDate($dto->getPubPublicateDay());
        $pub->setPubRoute($dto->getPubRoute());
        if ($dto->getPubImage()) {
            $date = date("Ymd_Hsi");
            $oname = $dto->getPubImage()->getClientOriginalName();
            $dto->getPubImage()->move(
                "pubs/images/",
                $date.$oname
            );
            $pub->setPubImage($date.$oname);
        }
        $this->em->persist($pub);
        $this->em->flush();
        $year = explode("/", $pub->getPubRoute())[0];
        if (!file_exists("pubs/".$pub->getPubRoute())) mkdir("pubs/".$pub->getPubRoute(), 0777, true);
        /*if (!file_exists("pubs/".$year."/.htaccess")) {
            file_put_contents("pubs/".$year."/.htaccess", "# Removes index.php from ExpressionEngine URLs
RewriteEngine on

# RewriteCond %{REQUEST_URI} !^/public/
RewriteRule ^(.*) /public/$1/index.html [L]");
        }*/
        if (!file_exists("pubs/".$pub->getPubRoute()."/index.html")) file_put_contents("pubs/".$pub->getPubRoute()."/index.html", "Page under uploading...");
        return true;
    }

    public function delPublication(int $pubId): bool
    {
        $pub = $this->getPublicationById($pubId);
        $this->em->remove($pub);
        $this->em->flush();
        return true;
    }

    public function getPublicationRoute(int $pubId): string
    {
        $pub = $this->getPublicationById($pubId);
        $pub->incrementPubViews();
        $this->em->persist($pub);
        $this->em->flush();
        return "/pubs/".$pub->getPubRoute()."/index.html";
    }

    public function getForm(PublicationDto $pubModel): FormInterface {
        $form = $this->formFactory->createBuilder(FormType::class, $pubModel);
        $form->add('pub_image', FileType::class, [
            'required'=>false, 'label'=>"K??pfelt??lt??s", 'constraints'=>[
                new File([
                    'mimeTypes' => [
                        "image/jpeg",
                        "image/png",
                        "image/jpg"
                    ],
                    'mimeTypesMessage' => "K??rem egy jpg/jpeg/png Objektumot t??lts??n fel."
                ])
            ]
        ]);
        $form->add('pub_name', TextType::class, [ 'required' => true, 'label' => "??j megjelen??s megnevez??se",
            'attr' => [
                'placeholder' => "Kiemelt c??m megad??sa"
            ]]);
        $form->add('pub_text', TextareaType::class, [
            'required' => false,
            'label' => "Megjelen??s sz??veges le??r??sa",
            'help' => "Ez a sz??veg jelenik meg a c??m alatt, ha ki van t??ltve.",
            'attr' => [
                'oninput' => "if (this.scrollHeight < 500) {this.style.height = ``;this.style.height = this.scrollHeight + 3 + `px`}",
                'placeholder' => "Le??r??s sz??vege"
            ],
            'constraints' => [
                new Length([
                    'max' => 10000,
                    'maxMessage' => "Az ??zenet maxim??lis hossz??s??ga {{ limit }} karakter! Ha enn??l hosszabban szeretne fogalmazni k??rem t??ltse fel .zip form??tumban."
                ])
            ]
        ]);
        $now = (new \DateTime("now"))->format("Y/M");
        $past_past_month = (new \DateTime("-2 month"))->format("Y/M");
        $past_month = (new \DateTime("-1 month"))->format("Y/M");
        $next_month = (new \DateTime("+1 month"))->format("Y/M");
        $dates = array();
        foreach ([$past_past_month, $past_month,  $now, $next_month] as $date) {
            if ($date == $now) {
                if (file_exists("pubs/".$date)) $dates["// ".$date." (exists) (now)"] = $date;
                else $dates[$date." (now)"] = $date;
            }
            else {
                if (file_exists("pubs/".$date)) $dates["// ".$date." (exists)"] = $date;
                else $dates[$date] = $date;
            }
        }

        $form->add('pub_route', ChoiceType::class, [
           'required' => true,
           'label' => "V??lasszon mapp??t",
           'help' => "Ide kell felt??lteni a dokumentumokat.",
           'choices' => $dates,
           'data' => $now
        ]);
        $form->add('pub_publicate_day', DateType::class, [
            'label' => "Megjelen??si id??",
            'help' => "Ilyenkor lesz publikusan l??that??.",
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',
            'data' => new \DateTime()
        ]);
        $form->add('pub_visible', CheckboxType::class, [
            'label' => "L??that??",
            'help' => "Ha ez a mez?? be van jel??lve akkor a megjen??st, a Megjelen??si id?? ut??n mindenki l??thatja.",
            'value' => true,
            'required' => false
        ]);
        $form->add('V??gleges??t', SubmitType::class, ['attr'=>['style'=>"width: 300px; margin: 30px 0px;"]]);
        return $form->getForm();
    }

    public function updatePublication(Publication $pub): void
    {
        $this->em->persist($pub);
        $this->em->flush();
    }

    public function getEditorForm(Publication $pub): FormInterface
    {
        $form = $this->formFactory->createBuilder(FormType::class, $pub);
        $form->add('pub_name', TextType::class, [ 'required' => true, 'label' => "Megjelen??s megnevez??se",
            'attr' => ['maxlength' => "150"]
        ]);
        $form->add('pub_text', TextareaType::class, [
            'required' => false,
            'label' => "Megjelen??s sz??veges le??r??sa",
            'help' => "Ez a sz??veg jelenik meg a c??m alatt, ha ki van t??ltve.",
            'attr' => ['oninput' => "if (this.scrollHeight < 500) {this.style.height = ``;this.style.height = this.scrollHeight + 3 + `px`}"],
            'constraints' => [
                new Length([
                    'max' => 5000,
                    'maxMessage' => "Az le??r??s maxim??lis hossz??s??ga {{ limit }} karakter!"
                ])
            ]
        ]);
        $now = (new \DateTime("now"))->format("Y/M");
        $past_month = (new \DateTime("-1 month"))->format("Y/M");
        $next_month = (new \DateTime("+1 month"))->format("Y/M");
        $form->add('pub_date', DateType::class, [
            'label' => "Megjelen??si id??",
            'help' => "Ilyenkor lesz publikusan l??that??.",
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',
            'data' => new \DateTime()
        ]);
        $form->add('pub_visible', CheckboxType::class, [
            'label' => "L??that??",
            'help' => "Ha ez a mez?? be van jel??lve akkor a megjen??st, a Megjelen??si id?? ut??n mindenki l??thatja."
        ]);
        $form->add('Submit', SubmitType::class);
        return $form->getForm();
    }
}