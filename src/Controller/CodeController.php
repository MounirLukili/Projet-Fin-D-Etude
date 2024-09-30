<?php

namespace App\Controller;

use App\Service\OpenAIService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CodeController
{
    private $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }
    
    #[Route('/run_code', name: 'run_code', methods: ['POST'])]


    public function runCode(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $code = $data['code'] ?? '';

        if (empty($code)) {
            return new JsonResponse(['output' => 'No code provided'], 400);
        }

        $output = $this->openAIService->runCode($code);

        return new JsonResponse(['output' => $output]);
    }
}
