<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserCrudController extends AbstractCrudController implements EventSubscriberInterface
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['hashPassword'],
            BeforeEntityUpdatedEvent::class => ['hashPassword'],
        ];
    }

    public function hashPassword($event)
    {
        $entity = $event->getEntityInstance();
        if ($entity instanceof User) {
            $hashedPassword = $this->passwordHasher->hashPassword($entity, $entity->getPassword());
            $entity->setPassword($hashedPassword);
            error_log('Password hashed: ' . $hashedPassword); // Log inside the conditional block
        } else {
            error_log('Entity is not of type User'); // Provides clarity that the entity processed was not a User
        }
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Users')
            ->setEntityLabelInSingular('User')
            ->setPageTitle("index", "Liste De Tous Les Participants")
            ->setPaginatorPageSize(10);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            TextField::new('nom'),
            TextField::new('prenom'),
            TextField::new('email'),
            TextField::new('password', 'Password')
                ->setPermission('ROLE_USER')
                ->hideOnIndex()
                ->setFormType(PasswordType::class) // Ensures the field type is password
                ->setFormTypeOption('always_empty', true),
            ChoiceField::new('role')
                ->setChoices([
                    'Admin' => 'ROLE_ADMIN',
                    'Student' => 'ROLE_STUDENT',
                    'Teacher' => 'ROLE_TEACHER'
                ])
                ->allowMultipleChoices(false) // Adjust according to your needs (true if multiple roles are allowed)
                ->renderExpanded(false), // Set to true if you want radio buttons instead of a dropdown
            TextField::new('adresse')
                ->hideOnIndex()->hideWhenUpdating(),
            TextField::new('numerotel')->hideWhenUpdating()
        ];
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $queryBuilder->andWhere('entity.role != :role')
                     ->setParameter('role', 'ROLE_ADMIN');

        // Filter by role if role parameter is set
        if (isset($filters['role'])) {
            $role = $filters['role']->getValue();
            $queryBuilder->andWhere('entity.role = :filteredRole')
                         ->setParameter('filteredRole', $role);
        }

        // Filter by nom (name) if nom parameter is set
        if (isset($filters['nom'])) {
            $nom = $filters['nom']->getValue();
            $queryBuilder->andWhere('entity.nom LIKE :filteredNom')
                         ->setParameter('filteredNom', '%' . $nom . '%');
        }

        return $queryBuilder;
    }

    
}
