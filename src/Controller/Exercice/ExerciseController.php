<?php

namespace App\Controller\Exercice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExerciseController extends AbstractController
{
    #[Route('/start-exercise-page', name: 'start_exercise_page')]
    public function startExercisePage(): Response
    {
        return $this->render('exercise/start.html.twig');
    }
}
