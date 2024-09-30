<?php


// src/Controller/ResultsController.php
namespace App\Controller\Exercice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface; // Import SessionInterface

class ScoreController extends AbstractController
{
    #[Route('/results', name: 'quiz_results')]
    public function index(SessionInterface $session): Response
{
    $results = json_decode($session->get('results', '[]'), true);
    $userAnswers = json_decode($session->get('userAnswers', '[]'), true);
    $score = $session->get('score', 0);

    // assure que userAnswers n'est pas vide sinon default
    foreach ($results as $index => $result) {
        if (!array_key_exists($index, $userAnswers)) {
            $userAnswers[$index] = 'No Answer Provided'; // Default si pas de answer
        }
    }

    return $this->render('exercise/results.html.twig', [
        'results' => $results,
        'userAnswers' => $userAnswers,
        'score' => $score
    ]);
}

}

