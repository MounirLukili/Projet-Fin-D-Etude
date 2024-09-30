<?php
// src/Controller/ScoreController.php

namespace App\Controller\Exercice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Score;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Sujets;

class SController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/api/score', name: 'api_score_save', methods: ['POST'])]
public function saveScore(Request $request, EntityManagerInterface $entityManager): Response
{
    $data = json_decode($request->getContent(), true);
    $user = $this->security->getUser();

    if (!$user) {
        return $this->json(['message' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
    }

    $score = new Score();
    $score->setNote($data['note']);
    $score->setNiveau($data['niveau']); 
    $score->setDate(new \DateTime()); 
    $score->setStudent($user);
     

     $sujet = $entityManager->getRepository(Sujets::class)->find($data['sujetId']);
      if ($sujet) {
       $score->setSujet($sujet);
      } else {
    
        return $this->json(['message' => 'Sujet not found'], Response::HTTP_BAD_REQUEST);
     }


      $entityManager->persist($score);
      $entityManager->flush();

      return $this->json(['message' => 'Score saved successfully']);
}

}
