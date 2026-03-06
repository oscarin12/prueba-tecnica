<?php

namespace App\Controller\Admin;

use App\Entity\WorkOrder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class WorkOrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WorkOrder::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Orden de trabajo')
            ->setEntityLabelInPlural('Órdenes de trabajo')
            ->setPageTitle(Crud::PAGE_INDEX, 'Órdenes de trabajo')
            ->setPageTitle(Crud::PAGE_NEW, 'Crear orden de trabajo')
            ->setPageTitle(Crud::PAGE_EDIT, 'Editar orden de trabajo')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Detalle de la orden')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),

            TextField::new('title', 'Título'),
            TextEditorField::new('description', 'Descripción')->hideOnIndex(),

            TextField::new('status', 'Estado'),

            AssociationField::new('assignedTo', 'Técnico'),

            DateTimeField::new('createdAt', 'Creada el')->onlyOnIndex(),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(
                ChoiceFilter::new('status', 'Estado')->setChoices([
                    'Asignada' => 'ASSIGNED',
                    'En curso' => 'IN_PROGRESS',
                    'Finalizada' => 'DONE',
                ])
            )
            ->add(EntityFilter::new('assignedTo', 'Técnico'));
    }
}