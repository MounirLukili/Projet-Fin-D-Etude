<?php

namespace App\Controller\Student;

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

class s_SettingsCrudController extends AbstractCrudController
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
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $qb->andWhere('entity.id = :id')                                  //récupérer l'id de l'étudiant connectéé
           ->setParameter('id', $this->security->getUser()->getId());
        return $qb;
    }

    public function createQuery(string $action): QueryBuilder
    {
        $queryBuilder = parent::createQuery($action);                               //recupérer les informations de l'étudiant connecté à partir de l'id
        $queryBuilder->andWhere('entity.id = :id')                                  //récupéré
                     ->setParameter('id', $this->security->getUser()->getId());
        return $queryBuilder;
    }

    
    public function configureFields(string $pageName): iterable
{
    $fields = [
        IdField::new('id')->hideOnForm()->hideOnIndex(),
        TextField::new('nom')->setPermission('ROLE_USER'),
        TextField::new('prenom')->setPermission('ROLE_USER'),             //permet le bon affichage des informations à modifier
        TextField::new('email')->setPermission('ROLE_USER'),
        TextField::new('password', 'Password')
            ->setPermission('ROLE_USER')
            ->hideOnIndex()
            ->setFormType(PasswordType::class) 
            ->setFormTypeOption('always_empty', true), 
        TextField::new('adresse')->setPermission('ROLE_USER'),
        TextField::new('numerotel')->setPermission('ROLE_USER'),
        TextField::new('role')->onlyOnDetail()->setPermission('ROLE_ADMIN') // Visible uniquement pour les admins et non editable
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



}