<?php

namespace App\Controller\Teacher;

use App\Entity\Score;
use App\Entity\User;
use App\Entity\Exercice;
use App\Entity\Sujets;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class TeacherDashBoardController extends AbstractDashboardController
{
    private $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    #[Route('/teacher', name: 'teacher_dashboard')]
    public function index(): Response
    {
        return $this->render('teacher/teacherdash.html.twig');
    }

    #[Route('/teacher/stats', name: 'stats_teacher')]
    public function stats(EntityManagerInterface $entityManager): Response
    {
        $this->configureAssets()->addCssFile('css/stats.css');

        // Fetch the scores from the database
        $scores = $entityManager->getRepository(Score::class)->findAll();

        // Process the data to be used in the chart
        $data = $this->processScoresData($scores);

        return $this->render('teacher/stats.html.twig', [
            'data' => $data,
        ]);
    }

    private function processScoresData($scores)
    {
        $studentData = [];

        // Organize scores by student and subject
        foreach ($scores as $score) {
            $studentId = $score->getStudent()->getId();
            $subject = $score->getSujet()->getModule();

            if (!isset($studentData[$studentId])) {
                $studentData[$studentId] = [];
            }

            if (!isset($studentData[$studentId][$subject])) {
                $studentData[$studentId][$subject] = [];
            }

            $studentData[$studentId][$subject][] = (float)$score->getNote();
        }

        // Calculate each student's average per subject
        $subjectAverages = [];
        foreach ($studentData as $studentId => $subjects) {
            foreach ($subjects as $subject => $notes) {
                if (!isset($subjectAverages[$subject])) {
                    $subjectAverages[$subject] = [];
                }
                $average = array_sum($notes) / count($notes);
                $subjectAverages[$subject][] = $average;
            }
        }

        // Calculate overall average per subject
        $chartData = [];
        foreach ($subjectAverages as $subject => $averages) {
            $overallAverage = array_sum($averages) / count($averages);
            $chartData[] = [
                'subject' => $subject,
                'average' => $overallAverage,
            ];
        }

        return $chartData;
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Espace Enseignant')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Accueil', 'fa fa-home')->setCssClass('custom-menu-item');     
        yield MenuItem::linkToCrud('Liste Des Etudiants', 'fas fa-list', User::class)
            ->setController(t_UserCrudController::class);
        yield MenuItem::linkToCrud('Notes Obtenues', 'fas fa-star', Score::class)
            ->setController(t_ScoreCrudController::class);
        yield MenuItem::linkToRoute('Statistiques', 'fas fa-bar-chart', 'stats_teacher')
            ->setQueryParameter('role', 'ROLE_TEACHER');
        yield MenuItem::linkToCrud('Exercices', 'fas fa-briefcase', Exercice::class)
            ->setController(t_ExerciceCrudController::class);
        yield MenuItem::linkToCrud('Sujets', 'fas fa-school', Sujets::class)
            ->setController(t_SujetsCrudController::class);
        yield MenuItem::linkToCrud('Paramètre Du Compte', 'fas fa-cog', User::class)
            ->setController(t_SettingsCrudController::class);
        
    }
}
