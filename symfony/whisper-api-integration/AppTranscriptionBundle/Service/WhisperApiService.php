<?php

namespace AppTranscriptionBundle\Service;

use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

class WhisperApiService
{
    private $client;
    private $apiKey;
    private $apiUrl;

    private LoggerInterface $logger;

    public function __construct(
        ClientInterface $client,
        string $apiUrl,
        string $apiKey,
        LoggerInterface $logger)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
        $this->logger = $logger;
    }

    public function transcribe(string $filePath, $prompt = ''): array
    {
        $data['file_path'] = $filePath;

        try {

            $multipart = [
                [
                    'name' => 'file',
                    'contents' => fopen($filePath, 'r'),
                    'filename' => basename($filePath),
                ],
                [
                    'name' => 'model',
                    'contents' => 'whisper-1',
                ],
            ];

            if ($prompt) {
                $multipart[] = [
                    'name' => 'prompt',
                    'contents' => $prompt,
                ];
            }

            $response = $this->client->request('POST', $this->apiUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                    ],
                    'multipart' => $multipart
                ]

            );

            $body = $response->getBody()->getContents();
            $decodedResponse = json_decode($body, true);
            $data['text'] = $decodedResponse['text'] ?? null;

            return $data;
        } catch (\Exception $e) {
            $message = 'Error during transcription audio ' . $filePath . ' : ' . $e->getMessage();
            $this->logger->error($message);
            throw new \Exception($message);
        }
    }
}
