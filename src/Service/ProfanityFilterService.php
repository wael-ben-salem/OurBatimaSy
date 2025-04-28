<?php

// src/Service/ProfanityFilterService.php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProfanityFilterService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;
    private string $apiUrl;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = 'gIHHFkHnq7ZoSD46N6MG/g==jBAL5pIyTfP8XOJJ';
        $this->apiUrl = 'https://api.api-ninjas.com/v1/profanityfilter';
    }

    public function filterText(string $text): string
    {
        try {
            $response = $this->httpClient->request('GET', $this->apiUrl, [
                'headers' => [
                    'X-Api-Key' => $this->apiKey,
                ],
                'query' => [
                    'text' => $text,
                ],
            ]);

            $data = $response->toArray();

            return $data['censored'] ?? $text;
        } catch (\Exception $e) {
            // If the API fails, return the original text
            return $text;
        }
    }
}