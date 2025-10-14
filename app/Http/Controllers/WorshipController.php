<?php

namespace App\Http\Controllers;

use App\Models\MorningWorship;
use App\Services\GeminiAIService;
use App\Services\WorshipService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorshipController extends Controller
{
    protected GeminiAIService $geminiAIService;

    public function __construct(GeminiAIService $geminiAIService)
    {
        $this->geminiAIService = $geminiAIService;
    }

    /**
     * Mostra a página de uma adoração individual.
     *
     * @param  MorningWorship  $worship
     * @return View
     */
    public function show(Request $request, MorningWorship $worship, WorshipService $worshipService): View
    {
        $playlist = $request->boolean('playlist');
        $search = $request->input('search');
        $searchInSubtitles = $request->boolean('search_in_subtitles');
        $watchedOnly = $request->boolean('watched');

        $nextWorshipId = null;
        $firstWorshipId = null;

        if ($playlist) {
            $nextWorshipId = $worshipService->getNextWorshipId($worship, $search, $searchInSubtitles, $watchedOnly);
            $firstWorshipId = $worshipService->getFirstWorshipId($search, $searchInSubtitles, $watchedOnly);
        }

        return view('worships.show', compact('worship', 'playlist', 'nextWorshipId', 'firstWorshipId'));
    }

    /**
     * Faz uma requisição para a API da Gemini AI para gerar um resumo conciso
     * da adoração matinal, com base nas legendas fornecidas.
     *
     * @param MorningWorship $worship Adoração matinal para gerar o resumo.
     *
     * @return JsonResponse Resposta em formato JSON com o resumo da adoração.
     *                      Se houver erro, retorna um erro HTTP 400 ou 500 com
     *                      uma mensagem de erro.
     */
    public function summarize(MorningWorship $worship): JsonResponse
    {
        $textToSummarize = $worship->subtitles_text;
        $titleMorningWorship = $worship->title;

        if (empty($textToSummarize)) {
            return response()->json(['error' => 'Legendas não encontradas para esta adoração.'], 400);
        }

        $prompt = 'Com base no conteúdo fornecido, forneça um resumo conciso dessa Adoração Matinal das Testemunhas de Jeová, **formatado em Markdown**. Inicie o resumo com o título da adoração ' . $titleMorningWorship . ', seguido de uma linha em branco. O título da adoração deve ser destacado em negrito. A estrutura do resumo deve ser a seguinte, depois do título: 1. Pequena introdução 2. Lições principais 3. Frases Marcantes. Os parágrafos devem ser formatados em Markdown. Separe as lições principais por ordenação númerica e identifique frases marcantes em *itálico* dessa adoração. Identifique e separe as lições principais das frases marcantes, com subtítulos.';

        $truncatedText = $this->geminiAIService->truncateText($textToSummarize);

        $summary = $this->geminiAIService->summarizeText($truncatedText, $prompt);

        if ($summary) {
            return response()->json(['summary' => trim($summary)]);
        } else {
            return response()->json(['error' => 'Erro ao gerar resumo com a IA.'], 500);
        }
    }
}
