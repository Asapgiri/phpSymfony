<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormFactoryInterface;

abstract class CrudService {
    /** @var EntityManagerInterface */
    protected $em;

    /** @var FormFactoryInterface */
    protected $formFactory;

    /**
     * CrudService constructor.
     * @param EntityManagerInterface $em
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        // $request => RequestStack $request->getCurreentRequest()
    }

    /**
     * @return EntityRepository
     * Return the repository of the service ...
     */
    public abstract function getRepo() : EntityRepository;
}