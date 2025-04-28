<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeminiApiService
{
    private $httpClient;
    private $apiKey;
    private $apiUrl;

    public function __construct(HttpClientInterface $httpClient, string $apiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
        $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key='.$apiKey;
    }

    public function getEstimation(string $styleArch, string $superficie, string $emplacement, string $type): string
    {
        try {
            $json = json_encode([
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            [
                                'text' => "Estimate the cost and duration for a project with the following details: " .
                                "Style Architecture: $styleArch, Superficie: $superficie, Emplacement: $emplacement, Type: $type. " .
                                "Please respond in French."
                            ]
                        ]
                    ]
                ]
            ]);

            $response = $this->httpClient->request('POST', $this->apiUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => $json
            ]);

            if ($response->getStatusCode() === 200) {
                return $response->getContent();
            } else {
                return json_encode(['error' => 'API request failed with status code: '.$response->getStatusCode()]);
            }
        } catch (\Exception $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
    }
}