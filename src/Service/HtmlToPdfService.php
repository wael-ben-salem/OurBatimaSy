<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class HtmlToPdfService
{
    public function __construct(
        private string $apiKey='oSSriPxgzKuGbk0Abx137RyhO7HEBYX6BIK6twgZaFYnxPh79ABzp5rsNbCCqa0a',
        private HttpClientInterface $httpClient
    ) {

        $this->apiKey = $apiKey;
        $this->httpClient = $httpClient;

    }
   

    public function generatePdf(string $html): string
    {
        try {
            $response = $this->httpClient->request('POST', 'https://api.html2pdf.app/v1/generate', [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => [
                    'html' => $html,
                    'apiKey' => $this->apiKey,
                    'format' => 'A4',
                    'marginTop' => 20,
                    'marginBottom' => 20,
                    'marginLeft' => 20,
                    'marginRight' => 20,
                ],
            ]);

            return $response->getContent();
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Failed to generate PDF: '.$e->getMessage());
        }
    }
}