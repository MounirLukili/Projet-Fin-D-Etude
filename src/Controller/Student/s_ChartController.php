<?php

namespace App\Controller\Student;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\SujetsRepository;
use App\Repository\ScoreRepository;

class s_ChartController extends AbstractController
{
    private $security;
    private $sujetsRepository;
    private $scoreRepository;

    public function __construct(Security $security, SujetsRepository $sujetsRepository, ScoreRepository $scoreRepository)
    {
        $this->security = $security;
        $this->sujetsRepository = $sujetsRepository;
        $this->scoreRepository = $scoreRepository;
    }


    #[Route('/student/charts', name: 'app_charts',)]

   
public function index(Request $request): Response
{
    $user = $this->security->getUser();
    $modules = $this->sujetsRepository->findAll();

    $selectedModule = $request->query->get('module');
    $scores = null;
    if ($selectedModule) {
        $scores = $this->scoreRepository->findByStudentAndModuleOrderedByDate($user, $selectedModule);
    }

    return $this->render('student/charts.html.twig', [
        'modules' => $modules,
        'scores' => $scores,
        'selectedModule' => $selectedModule
    ]);
}

}
