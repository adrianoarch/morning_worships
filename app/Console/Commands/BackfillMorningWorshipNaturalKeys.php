<?php

namespace App\Console\Commands;

use App\Models\MorningWorship;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class BackfillMorningWorshipNaturalKeys extends Command
{
    protected $signature = 'worships:backfill-natural-keys';
    protected $description = 'Atualiza as naturalKeys para registros existentes na base de dados';

    private $apiUrl = 'https://b.jw-cdn.org/apis/mediator/v1/categories/T/VODPgmEvtMorningWorship?detailed=1&mediaLimit=0&clientType=www';

    public function handle()
    {
        $this->info('Iniciando processo de backfill para languageAgnosticNaturalKeys...');

        try {
            $response = Http::get($this->apiUrl);
            $data = $response->throw()->json();

            $totalAtualizados = 0;
            $totalRegistros = count($data['category']['media']);

            $this->output->progressStart($totalRegistros);

            foreach ($data['category']['media'] as $item) {
                $atualizado = MorningWorship::where('guid', $item['guid'])
                    ->update([
                        'natural_key' => $item['languageAgnosticNaturalKey']
                    ]);

                if ($atualizado) {
                    $totalAtualizados++;
                }

                $this->output->progressAdvance();
            }

            $this->output->progressFinish();

            $this->info("\nBackfill concluído com sucesso!");
            $this->info("Total de registros atualizados: {$totalAtualizados}/{$totalRegistros}");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("\nErro durante o backfill: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
