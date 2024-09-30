<?php

namespace App\Controller\Student;

use App\Entity\Score;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

class s_ScoreCrudController extends AbstractCrudController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getEntityFqcn(): string
    {
        return Score::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $qb->andWhere('entity.student = :student')
           ->setParameter('student', $this->security->getUser());
        return $qb;
    }

    public function createQuery(string $action): QueryBuilder
    {
        $queryBuilder = parent::createQuery($action);       //permet de récupérer uniquement les notes de l'étudiant 
        $queryBuilder->andWhere('entity.student = :student')         //conecté
                     ->setParameter('student', $this->security->getUser());
        return $queryBuilder;
    }

    public function configureFields(string $pageName): iterable
{
    return [
        IdField::new('id')->hideOnForm()
        ->hideOnIndex()->hideOnDetail(),
        DateTimeField::new('date')
            ->setFormat('dd/MM/yyyy'),
            TextField::new('sujet')                                  //permet d'afficher les détails des notes de 
        ->formatValue(function ($value, $entity) {                    //l'étudiant connecté
            return $entity->getSujet()->getModule(); 
        }), TextField::new('niveau'),
        AssociationField::new('student')->hideOnIndex()
            ->setCrudController(s_UserCrudController::class),
        
        TextField::new('note')
        
       
    ];
}


    public function configureActions(Actions $actions): Actions
{
    return $actions
        ->disable(Action::NEW, Action::EDIT, Action::DELETE)
         // Correct usage of the add() method
        ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
            return $action->setIcon('fa fa-edit')->addCssClass('btn btn-primary');
        });
}

public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPaginatorPageSize(10) // Set the number of items per page to 10
            ->setPageTitle(Crud::PAGE_INDEX, 'Mes Notes');
    }


}
