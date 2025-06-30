<?php

namespace App\Console\Commands;

use App\Models\MorningWorship;
use App\Traits\VttTextExtractor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateMorningWorships extends Command
{
    use VttTextExtractor;

    protected $signature = 'worships:update';
    protected $description = 'Atualiza a lista de adorações matinais do JW.org';

    private $apiUrl = 'https://b.jw-cdn.org/apis/mediator/v1/categories/T/VODPgmEvtMorningWorship?detailed=1&mediaLimit=0&clientType=www';

    public function handle()
    {
        $this->info('Iniciando atualização das adorações matinais...');

        try {
            $response = Http::get($this->apiUrl);
            $data = $response->json();

            foreach ($data['category']['media'] as $item) {
                $video720p = collect($item['files'])->firstWhere('label', '720p');
                $image = $item['images']['lss']['lg'] ?? null;
                $subtitle = $video720p['subtitles'] ?? null;

                // Atualiza ou cria o registro
                $morningWorship = MorningWorship::updateOrCreate(
                    ['guid' => $item['guid']],
                    [
                        'natural_key' => $item['languageAgnosticNaturalKey'],
                        'title' => $item['title'],
                        'description' => $item['description'],
                        'first_published' => $item['firstPublished'],
                        'duration' => $item['duration'],
                        'duration_formatted' => $item['durationFormattedMinSec'],
                        'video_url' => $video720p['progressiveDownloadURL'] ?? null,
                        'image_url' => $image,
                        'subtitles' => $subtitle
                    ]
                );

                // Se houver legenda e ela possuir a URL, processa o conteúdo
                if ($video720p && isset($video720p['subtitles']['url'])) {
                    $subtitleUrl = $video720p['subtitles']['url'];

                    try {
                        // Configure HTTP client with proper timeouts and retries
                        $subResponse = Http::timeout(30)
                            ->retry(3, 1000) // 3 retries with 1 second delay
                            ->withHeaders([
                                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                            ])
                            ->get($subtitleUrl);
                            
                        if ($subResponse->successful()) {
                            $vttContent = $subResponse->body();
                            $cleanText = $this->extractTextFromVtt($vttContent);
                            $morningWorship->update(['subtitles_text' => $cleanText]);
                        } else {
                            $this->warn("Falha ao baixar legenda para {$item['guid']}: HTTP {$subResponse->status()}");
                        }
                    } catch (\Exception $e) {
                        $this->error("Erro ao processar legenda para {$item['guid']}: " . $e->getMessage());
                    }
                }
            }

            $this->info('Atualização concluída com sucesso!');
        } catch (\Exception $e) {
            $this->error('Erro ao atualizar adorações: ' . $e->getMessage());
        }
    }
}
