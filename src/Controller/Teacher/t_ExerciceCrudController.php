<?php

namespace App\Controller\Teacher;

use App\Entity\Exercice;
use App\Form\ExerciceType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use App\Entity\Sujets;

class t_ExerciceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Exercice::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            AssociationField::new('sujet')->setRequired(true)->autocomplete(),
            ChoiceField::new('niveau')->setChoices(['1' => '1', '2' => '2', '3' => '3']),
            ChoiceField::new('type')->setChoices(['QCM' => 'QCM', 'FTB' => 'FTB', 'OQ' => 'OQ']),
            TextField::new('contenu')->setFormTypeOptions([
                'attr' => [
                    'class' => 'custom-text-field',
                ],
            ])->setRequired(true),//->setHelp($this->getTooltipHelp('img/add.jpg')),
            TextField::new('solution')//->setHelp($this->getTooltipHelp('img/add.jpg')),
        ];
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setPaginatorPageSize(10);
    }

    public function configureActions(Actions $actions): Actions
    {
        $customAddAction = Action::new('customAdd', 'Ajouter Exercice')
            ->linkToCrudAction('customAdd')
            ->setCssClass('btn btn-success')
            ->createAsGlobalAction();

        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->add(Crud::PAGE_INDEX, $customAddAction);
    }

    #[Route('/teacher/exercice/custom-add', name: 'custom_exercice_add')]
public function customAdd(Request $request, EntityManagerInterface $entityManager): Response
{
    if ($request->isMethod('POST')) {
        $exercice = new Exercice();
        $sujetId = $request->request->get('hidden-sujet');
        $sujet = $entityManager->getRepository(Sujets::class)->find($sujetId);
        $exercice->setSujet($sujet);
        $exercice->setNiveau($request->request->get('hidden-niveau'));
        $exercice->setType($request->request->get('hidden-type'));

        switch ($request->request->get('hidden-type')) {
            case 'QCM':
                $contenu = json_encode([
                    $request->request->get('qcm_enonce'),
                    $request->request->get('qcm_solution1'),
                    $request->request->get('qcm_solution2'),
                    $request->request->get('qcm_solution3'),
                    $request->request->get('qcm_solution4')
                ]);
                $solution = json_encode([$request->request->get('qcm_reponse')]);
                break;
            case 'FTB':
                $contenu = json_encode([$request->request->get('ftb_contenu')]);
                $solution = [];
                if ($request->request->get('ftb_solution1')) {
                    $solution[] = $request->request->get('ftb_solution1');
                }
                if ($request->request->get('ftb_solution2')) {
                    $solution[] = $request->request->get('ftb_solution2');
                }
                if ($request->request->get('ftb_solution3')) {
                    $solution[] = $request->request->get('ftb_solution3');
                }
                if ($request->request->get('ftb_solution4')) {
                    $solution[] = $request->request->get('ftb_solution4');
                }
                $solution = json_encode($solution);
                break;
            case 'OQ':
                $contenu = json_encode([$request->request->get('oq_contenu')]);
                $solution = json_encode([
                    $request->request->get('oq_expected_output'),
                    $request->request->get('oq_correction')
                ]);
                break;
            default:
                $contenu = '';
                $solution = '';
                break;
        }

        $exercice->setContenu($contenu);
        $exercice->setSolution($solution);

        $entityManager->persist($exercice);
        $entityManager->flush();

        return $this->redirectToRoute('teacher_dashboard');
    }

   
    $sujets = $entityManager->getRepository(Sujets::class)->findAll();

    return $this->render('teacher/exercice/custom_add.html.twig', [
        'sujets' => $sujets,
    ]);
}


}
