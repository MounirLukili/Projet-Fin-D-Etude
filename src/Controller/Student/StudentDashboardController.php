<?php

namespace App\Controller\Student;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Score;
use App\Entity\Exercice;
use App\Entity\Sujets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\SujetsRepository;
use App\Repository\ScoreRepository;

class StudentDashboardController extends AbstractDashboardController
{
    private UrlGeneratorInterface $urlGenerator;
    private Security $security;
    private EntityManagerInterface $entityManager;
    private SujetsRepository $sujetsRepository;
    private ScoreRepository $scoreRepository;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        Security $security,
        EntityManagerInterface $entityManager,
        SujetsRepository $sujetsRepository,
        ScoreRepository $scoreRepository
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->sujetsRepository = $sujetsRepository;
        $this->scoreRepository = $scoreRepository;
    }

    #[Route('/student', name: 'student_dashboard')]
    public function index(): Response
    {
        return $this->render('student/studentdash.html.twig');
    }

    #[Route('/student/start', name: 'student_start')]
    public function start(): Response
    {
        $buttonUrl = $this->urlGenerator->generate('start_exercise_page');
        return $this->render('exercise/start.html.twig', [
            'buttonUrl' => $buttonUrl
        ]);
    }

    #[Route('/student/charts', name: 'student_charts')]
    public function charts(Request $request): Response
    {
        $user = $this->security->getUser();
        $modules = $this->sujetsRepository->findAll();

        $selectedModule = $request->query->get('module');
        $scores = [];
        if ($selectedModule) {
            $scores = $this->scoreRepository->findByStudentAndModuleOrderedByDate($user, $selectedModule);
        }

        return $this->render('student/charts.html.twig', [
            'modules' => $modules,
            'scores' => $scores,
            'selectedModule' => $selectedModule
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Espace Etudiant')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Acceuil', 'fa fa-home');     
        yield MenuItem::linkToCrud('Liste Des Participants', 'fas fa-list', User::class)
            ->setController(s_UserCrudController::class);
        yield MenuItem::linkToRoute('Commencer Un Quiz', 'fa fa-play','student_start');
        yield MenuItem::linkToCrud('Mes Notes', 'fas fa-star', Score::class)
            ->setController(s_ScoreCrudController::class);
        yield MenuItem::linkToRoute('Mes Stats', 'fa fa-chart-bar', 'student_charts');
        yield MenuItem::linkToCrud('Paramètre Du Compte', 'fas fa-cog', User::class)
            ->setController(s_SettingsCrudController::class);   
    }
}
