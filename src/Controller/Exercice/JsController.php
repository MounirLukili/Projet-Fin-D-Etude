<?php

namespace App\Controller\Exercice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ExerciceRepository;

class JsController extends AbstractController
{
    #[Route('/api/exercises', name: 'api_exercises')]
    public function getExercises(ExerciceRepository $exerciceRepository): JsonResponse
    {
        $exercises = $exerciceRepository->findAll();
        $data = [];

        foreach ($exercises as $exercise) {
            $sujet = $exercise->getSujet();
            error_log("Raw JSON: " . $exercise->getContenu());
            error_log("Decoded Array: " . print_r(json_decode($exercise->getContenu(), true), true));


            $decodedContent = json_decode($exercise->getContenu(), true);
            $decodedSolution = json_decode($exercise->getSolution(), true);

            // Log the decoded content and solution to ensure they are not arrays when treated as strings
            error_log("Decoded Content: " . json_encode($decodedContent));
            error_log("Decoded Solution: " . json_encode($decodedSolution));

            $data[] = [
                'id' => $exercise->getId(),
                'niveau' => $exercise->getNiveau(),
                'type' => $exercise->getType(),
                'contenu' => json_decode($exercise->getContenu(), true) ?: [],
                'solution' => json_decode($exercise->getSolution(), true) ?: [],
                'sujet' => [
                    'id' => $sujet->getId(),
                    'module' => $sujet->getModule()
                ]
            ];

            
        }

        return $this->json($data);
      
    }
     
}
