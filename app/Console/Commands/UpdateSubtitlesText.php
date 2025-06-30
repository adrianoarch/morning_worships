<?php

namespace App\Console\Commands;

use App\Models\MorningWorship;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Traits\VttTextExtractor;


class UpdateSubtitlesText extends Command
{
    use VttTextExtractor;

    protected $signature = 'worships:update-subtitles';
    protected $description = 'Baixa, limpa e atualiza o texto das legendas dos vídeos de adoração matinal';

    /**
     * Handle the command.
     *
     * @return void
     */
    public function handle()
    {

        $this->info('Iniciando atualização do texto das legendas...');

        MorningWorship::whereNotNull('subtitles')->chunk(100, function ($worships) {
            foreach ($worships as $worship) {

                $subtitlesData = $worship->subtitles ?? [];
                if (!isset($subtitlesData['url'])) {
                    $this->warn("Registro {$worship->id} não possui URL de legenda.");
                    continue;
                }

                $subtitleUrl = $subtitlesData['url'];

                try {
                    // Configure HTTP client with proper timeouts and retries
                    $response = Http::timeout(30)
                        ->retry(3, 1000) // 3 retries with 1 second delay
                        ->withHeaders([
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                        ])
                        ->get($subtitleUrl);
                        
                    if ($response->successful()) {
                        $vttContent = $response->body();
                        $cleanText = $this->extractTextFromVtt($vttContent);
                        $worship->update(['subtitles_text' => $cleanText]);
                        $this->info("Registro {$worship->id} atualizado com sucesso.");
                    } else {
                        $this->error("Falha ao baixar legenda para registro {$worship->id}: HTTP {$response->status()}");
                    }
                } catch (\Exception $e) {
                    $this->error("Erro no registro {$worship->id}: " . $e->getMessage());
                }
            }
        });

        $this->info('Atualização das legendas concluída!');
    }

}
