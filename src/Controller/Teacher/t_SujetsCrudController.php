<?php

namespace App\Controller\Teacher;

use App\Entity\Sujets;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class t_SujetsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sujets::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnIndex()->hideOnForm(),
            TextField::new('Module'),
            
        ];
    }
    
}
