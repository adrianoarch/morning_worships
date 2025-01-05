<?php

namespace App\Console\Commands;

use App\Models\MorningWorship;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateMorningWorships extends Command
{
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

                MorningWorship::updateOrCreate(
                    ['guid' => $item['guid']], // Busca por esse campo
                    [
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
            }

            $this->info('Atualização concluída com sucesso!');
        } catch (\Exception $e) {
            $this->error('Erro ao atualizar adorações: ' . $e->getMessage());
        }
    }
}
