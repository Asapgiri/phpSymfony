<?php
namespace App\Service;

use App\Entity\AdUnit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormFactoryInterface;

class AdUnitService extends CrudService implements AdUnitServiceInterface {
    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory)
    {
        parent::__construct($em, $formFactory);
    }

    public function getRepo(): EntityRepository
    {
        return $this->em->getRepository(AdUnit::class);
    }

    public function getAll(): array
    {
        return $this->getRepo()->findAll();
    }

    public function getOneById(int $id): AdUnit
    {
        return $this->getRepo()->findOneBy(['unit_id'=>$id]);
    }

}