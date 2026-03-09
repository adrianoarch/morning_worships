<?php

namespace App\Services;

use GeminiAPI\Client;
use GeminiAPI\Resources\ModelName;
use GeminiAPI\Resources\Parts\TextPart;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GeminiAIService
{
    protected Client $client;

    /**
     * @param null|Client|\Mockery\LegacyMockInterface|\Mockery\MockInterface $client
     */
    public function __construct(?Client $client = null)
    {
        $this->client = $client ?? new Client(config('services.gemini.api_key'));
    }

    /**
     * Summarize a given text using Gemini's 3.0 Preview model.
     *
     * @param string $text The text to summarize.
     * @param string $prompt A prompt to guide the summarization process.
     *
     * @return string|null The generated summary, or null if an error occurred.
     */
    public function summarizeText(string $text, string $prompt): ?string
    {
        try {
            $fullPrompt = $prompt . "\n\nConteúdo:\n" . $text;

            // $listModels = $this->client->listModels(); // para ver os modelos disponíveis
            // dd($listModels);

            $response = $this->client
                ->generativeModel(ModelName::GEMINI_3_1_FLASH_LITE)
                ->generateContent(
                    new TextPart($fullPrompt),
                );

            return $response->text();
        } catch (\Exception $e) {
            Log::error('Erro ao gerar resumo com Gemini API: ' . $e->getMessage());

            return $e->getMessage();
        }
    }

    /**
     * Truncates a given text to a maximum length, appending a '...' string if truncation occurs.
     *
     * @param string $text The text to truncate.
     * @param int $limit The maximum length of the returned string.
     *
     * @return string The truncated text.
     */
    public function truncateText(string $text, int $limit = 15000): string
    {
        if (strlen($text) <= $limit) {
            return $text;
        }

        return substr($text, 0, $limit) . '... (texto truncado devido ao limite da API)';
    }

    /**
     * Gera um desenho estilo chibi do orador da adoração usando o Gemini Image Generation.
     *
     * A lib gemini-api-php/client não suporta responseModalities, necessário para
     * geração de imagens, por isso fazemos a chamada HTTP diretamente.
     *
     * @param string $imageUrl URL pública da imagem de capa do vídeo (foto do orador).
     * @param string $prompt   Prompt para orientar a geração do desenho.
     *
     * @return array{image: string, mimeType: string}|null Dados da imagem gerada em base64 ou null em caso de erro.
     */
    public function generateDrawing(string $imageUrl, string $prompt): ?array
    {
        try {
            // 1. Baixar a imagem do orador (capa do vídeo)
            $speakerImageResponse = Http::timeout(30)->get($imageUrl);

            if ($speakerImageResponse->failed()) {
                Log::error('Erro ao baixar imagem do orador: HTTP ' . $speakerImageResponse->status());
                return ['error' => 'Erro ao baixar a imagem do orador (HTTP ' . $speakerImageResponse->status() . ').'];
            }

            $speakerImageBase64 = base64_encode($speakerImageResponse->body());
            $speakerMimeType = $speakerImageResponse->header('Content-Type') ?: 'image/jpeg';

            // 2. Ler a imagem de referência de estilo (Denis.png)
            $styleReferencePath = base_path('docs/refs/Denis.png');

            if (!file_exists($styleReferencePath)) {
                Log::error('Imagem de referência de estilo não encontrada: ' . $styleReferencePath);
                return ['error' => 'Imagem de referência de estilo não encontrada no servidor.'];
            }

            $styleImageBase64 = base64_encode(file_get_contents($styleReferencePath));

            // 3. Montar o payload da API
            $apiKey = config('services.gemini.api_key');
            $model = ModelName::GEMINI_3_0_IMAGE_PREVIEW;
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

            $payload = [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inlineData' => [
                                    'mimeType' => $speakerMimeType,
                                    'data' => $speakerImageBase64,
                                ],
                            ],
                            [
                                'inlineData' => [
                                    'mimeType' => 'image/png',
                                    'data' => $styleImageBase64,
                                ],
                            ],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'responseModalities' => ['TEXT', 'IMAGE'],
                ],
            ];

            // 4. Fazer a chamada à API
            $response = Http::timeout(120)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'x-goog-api-key' => $apiKey,
                ])
                ->post($url, $payload);

            if ($response->failed()) {
                $body = $response->json();
                $apiMessage = $body['error']['message'] ?? $response->body();
                Log::error('Erro na API Gemini Image Generation: HTTP ' . $response->status() . ' - ' . $response->body());
                return ['error' => 'Erro da API Gemini (HTTP ' . $response->status() . '): ' . Str::limit($apiMessage, 200)];
            }

            $data = $response->json();

            // 5. Extrair a imagem gerada da resposta
            $candidates = $data['candidates'] ?? [];

            if (empty($candidates)) {
                Log::error('Gemini Image Generation: nenhum candidato retornado.', ['response' => $data]);
                return ['error' => 'A IA não retornou nenhum resultado. Tente novamente.'];
            }

            $parts = $candidates[0]['content']['parts'] ?? [];

            foreach ($parts as $part) {
                if (isset($part['inlineData'])) {
                    return [
                        'image' => $part['inlineData']['data'],
                        'mimeType' => $part['inlineData']['mimeType'] ?? 'image/png',
                    ];
                }
            }

            Log::error('Gemini Image Generation: nenhuma imagem encontrada na resposta.', ['parts' => $parts]);
            return ['error' => 'A IA não retornou uma imagem na resposta. Tente novamente.'];
        } catch (\Exception $e) {
            Log::error('Erro ao gerar desenho com Gemini API: ' . $e->getMessage());
            return ['error' => 'Erro inesperado: ' . $e->getMessage()];
        }
    }
}

