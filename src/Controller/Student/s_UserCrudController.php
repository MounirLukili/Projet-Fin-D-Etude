<?php

namespace App\Controller\Student;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;

class s_UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {                                  
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex(),  
            TextField::new('nom'),                                           //permet d'afficher que quelques attributs pour des raisons
            TextField::new('prenom'),                                        //de sécurité et confidentialité
            TextField::new('role'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission('ROLE_STUDENT')  // Set this according to your security needs
            ->showEntityActionsInlined()  // Inline actions display
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste Des Participants');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT, Action::DELETE);  // Ensure actions are view-only
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $queryBuilder->andWhere('entity.role != :role')
                     ->setParameter('role', 'ROLE_ADMIN');

        $searchParameters = $searchDto->getAppliedFilters();

        // Filter by role if role parameter is set
        if (isset($searchParameters['role'])) {
            $role = $searchParameters['role']->getValue();
            $queryBuilder->andWhere('entity.role = :filteredRole')
                         ->setParameter('filteredRole', $role);
        }

        // Filter by nom (name) if nom parameter is set
        if (isset($searchParameters['nom'])) {
            $nom = $searchParameters['nom']->getValue();
            $queryBuilder->andWhere('entity.nom LIKE :filteredNom')
                         ->setParameter('filteredNom', '%' . $nom . '%');
        }

        return $queryBuilder;
    }
}
