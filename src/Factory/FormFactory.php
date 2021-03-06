<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Factory;

use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\CrudBatchActionFormType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\CrudFormType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FiltersFormType;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class FormFactory
{
    private $symfonyFormFactory;
    private $crudUrlGenerator;

    public function __construct(FormFactoryInterface $symfonyFormFactory, CrudUrlGenerator $crudUrlGenerator)
    {
        $this->symfonyFormFactory = $symfonyFormFactory;
        $this->crudUrlGenerator = $crudUrlGenerator;
    }

    public function createEditForm(EntityDto $entityDto, KeyValueStore $formOptions): FormInterface
    {
        $formOptions->set('entityDto', $entityDto);
        $formOptions->set('attr.id', sprintf('edit-%s-form', $entityDto->getName()));

        return $this->symfonyFormFactory->createNamedBuilder($entityDto->getName(), CrudFormType::class, $entityDto->getInstance(), $formOptions->all())->getForm();
    }

    public function createNewForm(EntityDto $entityDto, KeyValueStore $formOptions): FormInterface
    {
        $formOptions->set('entityDto', $entityDto);
        $formOptions->set('attr.id', sprintf('new-%s-form', $entityDto->getName()));

        return $this->symfonyFormFactory->createNamedBuilder($entityDto->getName(), CrudFormType::class, $entityDto->getInstance(), $formOptions->all())->getForm();
    }

    public function createBatchActionsForm(): FormInterface
    {
        return $this->symfonyFormFactory->createNamedBuilder('batch_form', CrudBatchActionFormType::class, null, [
            'action' => $this->crudUrlGenerator->build()->setAction('batch')->generateUrl(),
        ])->getForm();
    }

    public function createFiltersForm(FilterCollection $filters, Request $request): FormInterface
    {
        $filtersForm = $this->symfonyFormFactory->createNamed('filters', FiltersFormType::class, null, [
            'method' => 'GET',
            'action' => $request->query->get('referrer'),
            'ea_filters' => $filters,
        ]);

        return $filtersForm->handleRequest($request);
    }
}
