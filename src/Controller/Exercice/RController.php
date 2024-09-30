<?php
// src/Controller/ScoreController.php

namespace App\Controller\Exercice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class RController extends AbstractController
{
    #[Route('/results', name: 'show_results', methods: ['POST'])]
   // RController
public function showResults(Request $request, SessionInterface $session): JsonResponse
{
    return $this->render('exercise/results.html.twig');


}
}