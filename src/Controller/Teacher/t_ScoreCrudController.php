<?php

namespace App\Controller\Teacher;

use App\Entity\Score;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class t_ScoreCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Score::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
        ->disable(Action::NEW, Action::EDIT, Action::DELETE); 
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            AssociationField::new('student', 'Etudiant')->setFormTypeOption('disabled', 'disabled')->formatValue(function ($value, $entity) {
                return $entity->getStudent()->getNom(). " ". $entity->getStudent()->getPrenom(); // Assuming the Module entity has a getName() method
            }),
            TextField::new('sujet', 'Sujet')->setFormTypeOption('disabled', 'disabled')->formatValue(function ($value, $entity) {
                return $entity->getSujet()->getModule();
            }),
            TextField::new('niveau', 'Niveau')->setFormTypeOption('disabled', 'disabled'),
            TextField::new('note', 'Note')->setFormTypeOption('disabled', 'disabled'),
            
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        ->setPageTitle(Crud::PAGE_INDEX, 'Liste Des Notes')
            ->setPaginatorPageSize(10); // Set the number of items per page to 10
    }
}
