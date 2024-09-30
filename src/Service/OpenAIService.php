<?php
namespace App\Service;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;

class OpenAIService
{
    private $client;
    private $apiKey;

    public function __construct()
    {
        // Create a Guzzle client
        $this->client = new GuzzleClient();
        $this->apiKey = $_ENV['OPENAI_API_KEY']; // Your API key
    }

    public function runCode(string $code): string
    {
        $sentence = "Run this code and only give me its output. If there's a syntax error or any type of error, return the error.";

        $prompt = "$sentence\n\nCode:\n$code";

        try {
            // Send a POST request to the OpenAI API
            $response = $this->client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => $prompt],
                    ],
                ],
            ]);

            $body = json_decode($response->getBody(), true);
            return $body['choices'][0]['message']['content'] ?? 'No content returned.';
        } catch (RequestException $e) {
            return "Error: " . $e->getMessage();
        }
    }
}
