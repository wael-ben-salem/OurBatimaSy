<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeminiApiService
{
    private $httpClient;
    private $apiKey;
    private $apiUrl;
    private $imageApiUrl;


    public function __construct(HttpClientInterface $httpClient, string $apiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
        $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key='.$apiKey;
        $this->imageApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp-image-generation:generateContent?key='.$apiKey;
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

    public function generateProjectImage(string $styleArch, string $superficie, string $emplacement, string $type): array
    {
        try {
            $prompt = "Génère une image photoréaliste d'un projet architectural avec les caractéristiques suivantes:\n";
            $prompt .= "- Style architectural: $styleArch\n";
            $prompt .= "- Superficie: $superficie\n";
            $prompt .= "- Emplacement: $emplacement\n";
            $prompt .= "- Type de construction: $type\n\n";
            $prompt .= "L'image doit être réaliste, détaillée et professionnelle, montrant le bâtiment dans son environnement.";
    
            $json = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'responseModalities' => ["IMAGE"],
                    'temperature' => 0.5
                ]
            ];
    
            $jsonPayload = json_encode($json);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('JSON encoding error: ' . json_last_error_msg());
            }
    
            $response = $this->httpClient->request(
                'POST', 
                $this->imageApiUrl,
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'body' => $jsonPayload,
                    // Add timeout and other options if needed
                ]
            );
    
            $statusCode = $response->getStatusCode();
            $content = $response->getContent(false); // Get raw content without throwing exceptions
    
            if ($statusCode === 200) {
                $responseData = json_decode($content, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \RuntimeException('Invalid JSON response: ' . json_last_error_msg());
                }
    
                // Log the full response for debugging
                error_log('Gemini API Response: ' . print_r($responseData, true));
    
                if (isset($responseData['candidates'][0]['content']['parts'][0]['inlineData']['data'])) {
                    return [
                        'success' => true,
                        'image' => $responseData['candidates'][0]['content']['parts'][0]['inlineData']['data'],
                        'mimeType' => $responseData['candidates'][0]['content']['parts'][0]['inlineData']['mimeType'] ?? 'image/png'
                    ];
                } elseif (isset($responseData['error'])) {
                    return [
                        'success' => false,
                        'error' => 'API Error: ' . ($responseData['error']['message'] ?? 'Unknown error')
                    ];
                } else {
                    return [
                        'success' => false,
                        'error' => 'Unexpected API response format: ' . substr($content, 0, 200)
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'error' => "API request failed with status code $statusCode. Response: " . substr($content, 0, 200)
                ];
            }
        } catch (\Exception $e) {
            error_log('Gemini API Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}