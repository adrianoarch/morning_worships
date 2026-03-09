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
        $exactPhrase = $request->boolean('exact_phrase');

        $nextWorshipId = null;
        $firstWorshipId = null;

        if ($playlist) {
            $nextWorshipId = $worshipService->getNextWorshipId($worship, $search, $searchInSubtitles, $watchedOnly, $exactPhrase);
            $firstWorshipId = $worshipService->getFirstWorshipId($search, $searchInSubtitles, $watchedOnly, $exactPhrase);
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

    /**
     * Gera um desenho estilo chibi do orador da adoração matinal usando IA.
     *
     * @param MorningWorship $worship Adoração matinal para gerar o desenho.
     *
     * @return JsonResponse Resposta em formato JSON com a imagem gerada em base64.
     */
    public function generateDrawing(MorningWorship $worship): JsonResponse
    {
        if (empty($worship->image_url)) {
            return response()->json(['error' => 'Imagem de capa não encontrada para esta adoração.'], 400);
        }

        $prompt = 'Crie um desenho fofo, com base na referência anexa. Uma referência de estilo é o desenho que minha esposa faz, que também está anexo na segunda referência. Faça o desenho de frente, apenas o busto, como se estivesse dentro de uma polaroid, tentando manter as proporções do desenho que minha esposa faz. Tente captar particularidades e traços da imagem do irmão, que possam ser interessantes no desenho. O desenho deve ser feito em um fundo branco, como se estivesse dentro de uma polaroid. Não é necessário nenhum descrição abaixo da imagem. O resultado final deve se basear na imagem do orador, ou seja, o resultado deve ser o orador desenhado no estilo de desenho que minha esposa faz. Pense bastante para que o resultado seja o mais próximo possível do desenho que minha esposa faz. Tente captar particularidades e traços da imagem do irmão, que possam ser interessantes no desenho.';

        $result = $this->geminiAIService->generateDrawing($worship->image_url, $prompt);

        if (isset($result['image'])) {
            $dataUri = 'data:' . $result['mimeType'] . ';base64,' . $result['image'];
            return response()->json(['image' => $dataUri, 'success' => true]);
        }

        $errorMessage = $result['error'] ?? 'Erro ao gerar o desenho com a IA. Tente novamente.';
        return response()->json(['error' => $errorMessage], 500);
    }
}
