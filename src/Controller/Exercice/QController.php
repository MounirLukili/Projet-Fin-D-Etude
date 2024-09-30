<?php

namespace App\Controller\Exercice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sujets;
use App\Entity\Exercice;

class QController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/quiz-setup', name: 'quiz_setup')]
    public function quizSetup(): Response
    {
        // Récupérer tous les modules éxistants
        $moduleQueryBuilder = $this->entityManager->createQueryBuilder();
        $moduleQueryBuilder->select('DISTINCT s.module')
                           ->from(Sujets::class, 's');
        $modules = $moduleQueryBuilder->getQuery()->getResult();
        $moduleNames = array_map(function($module) {
            return $module['module'];
        }, $modules);

        // Récupérer les niveaux éxistants
        $levelQueryBuilder = $this->entityManager->createQueryBuilder();
        $levelQueryBuilder->select('DISTINCT e.niveau')
                          ->from(Exercice::class, 'e');
        $levels = $levelQueryBuilder->getQuery()->getResult();
        $levelNames = array_map(function($level) {
            return $level['niveau'];
        }, $levels);

        // Rendu de la page :
        return $this->render('exercise/quizaccueil.html.twig', [
            'modules' => $moduleNames,
            'levels' => $levelNames, 
        ]);
    }
}