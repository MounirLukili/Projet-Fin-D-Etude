<?php

namespace App\Controller\Admin;

use App\Entity\Score;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class ScoreCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Score::class;
    }


    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // Disable Create, Edit and Delete actions
            ->disable(Action::NEW, Action::EDIT, Action::DELETE);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // Optionally, you can set the page title for the detail view
            ->setPageTitle(Crud::PAGE_DETAIL, '%entity_label_singular% details')
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste Des Notes')
            
            ->setPaginatorPageSize(10); // Set the number of items per page to 10
    }
    
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()
            ->hideOnIndex()->hideOnDetail(),
            DateTimeField::new('date')
                ->setFormat('dd/MM/yyyy'),
            AssociationField::new('student')
            ->setFormTypeOptions([
                'choice_label' => 'Email',  // Use 'email' attribute for display
            ])
            ->formatValue(function ($value, $entity) {
                return $entity->getStudent()->getNom(). " ". $entity->getStudent()->getPrenom(); // Assuming the Module entity has a getName() method
            }),
            TextField::new('sujet')
            ->formatValue(function ($value, $entity) {
                return $entity->getSujet()->getModule();
            }),
            TextField::new('niveau')->hideOnIndex(),
            TextField::new('note'),
           
            
        ];
    }
    
}
