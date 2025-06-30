<?php

namespace App\Services;

use GeminiAPI\Client;
use GeminiAPI\Resources\ModelName;
use GeminiAPI\Resources\Parts\TextPart;
use Illuminate\Support\Facades\Log;

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
     * Summarize a given text using Gemini's 2.0 Flash model.
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
                ->generativeModel(ModelName::GEMINI_2_5_PRO_EXP)
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
}
