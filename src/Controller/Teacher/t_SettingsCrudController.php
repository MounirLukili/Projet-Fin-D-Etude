<?php

namespace App\Controller\Teacher;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class t_SettingsCrudController extends AbstractCrudController
{
    private $security;

    public function __construct(Security $security)
    {
        
        $this->security = $security;
        
    }

    public static function getEntityFqcn(): string
    {
        
        return User::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        dump("hey");
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $qb->andWhere('entity.id = :id')
           ->setParameter('id', $this->security->getUser()->getId());
        return $qb;
    }

    public function createQuery(string $action): QueryBuilder
    {
        $queryBuilder = parent::createQuery($action);
        $queryBuilder->andWhere('entity.id = :id')
                     ->setParameter('id', $this->security->getUser()->getId());
        return $queryBuilder;
    }



    public function configureFields(string $pageName): iterable
{
    $fields = [
        IdField::new('id')->hideOnForm()->hideOnIndex(),
        TextField::new('nom')->setPermission('ROLE_USER'),
        TextField::new('prenom')->setPermission('ROLE_USER'),
        TextField::new('email')->setPermission('ROLE_USER'),
        TextField::new('password', 'Password')
            ->setPermission('ROLE_USER')
            ->hideOnIndex()
            ->setFormType(PasswordType::class) // This ensures the field type is password
            ->setFormTypeOption('always_empty', true), // This makes sure the field is always empty on the forms
            //->setFormTypeOption('mapped', false), // Prevents the field from being mapped directly to the entity field
        TextField::new('adresse')->setPermission('ROLE_USER'),
        TextField::new('numerotel')->setPermission('ROLE_USER'),
        TextField::new('role')->onlyOnDetail()->setPermission('ROLE_ADMIN') // Visible only to admin or not editable
    ];

    return $fields;
}



public function configureActions(Actions $actions): Actions
{
    return $actions
        ->disable(Action::NEW, Action::DELETE)
        ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
            return $action->setIcon('fa fa-edit')->addCssClass('btn btn-primary');
        });
}


public function configureCrud(Crud $crud): Crud
    {
        return $crud
             
            ->setPageTitle(Crud::PAGE_INDEX, 'Paramètres Du Compte') ; // titre de la page
            
    }


}