<?php

namespace App\Http\Controllers;

use App\Services\WorshipService;
use App\Models\MorningWorship;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    protected WorshipService $worshipService;

    public function __construct(WorshipService $worshipService)
    {
        $this->worshipService = $worshipService;
    }

    /**
     * Exibe a página de adorações com paginação e contagem de assistidas.
     *
     * Este método verifica se o usuário está autenticado antes de recuperar
     * uma lista paginada das adorações. Se ocorrer algum erro durante a
     * recuperação, um erro 500 é lançado.
     *
     * @param Request $request Requisição HTTP contendo os parâmetros de pesquisa.
     * @return View Retorna a visão da página de adorações.
     * @throws \Exception Lança um erro se a recuperação das adorações falhar.
     */
    public function index(Request $request): View
    {
        if (Auth::guest()) {
            abort(403, 'Você precisa estar logado para acessar essa página.');
        }

        try {
            $watchedOnly = $request->boolean('watched');
            $worships = $this->worshipService->getPaginatedWorships(
                $request->search,
                $request->boolean('search_in_subtitles'),
                $watchedOnly
            );
            $watchedWorshipsCount = $this->worshipService->getWatchedWorshipsCount(
                $request->search,
                $request->boolean('search_in_subtitles'),
                $watchedOnly
            );
            $firstWorshipId = $this->worshipService->getFirstWorshipId(
                $request->search,
                $request->boolean('search_in_subtitles'),
                $watchedOnly
            );

            return view('worships.index', compact('worships', 'watchedWorshipsCount', 'firstWorshipId'));
        } catch (\Exception $e) {
            report($e);
            abort(500, 'Erro ao recuperar as adorações. Tente novamente mais tarde.');
        }
    }

    /**
     * Marca ou desmarca uma adoração como assistida.
     *
     * Verifica se o usuário que fez a requisição já assistiu a adoração
     * especificada. Se sim, remove o registro da adoração como assistida.
     * Se não, marca a adoração como assistida.
     *
     * @param Request $request
     * @param MorningWorship $worship
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleWatched(Request $request, MorningWorship $worship)
    {
        $user = $request->user();

        if ($worship->wasWatchedBy($user)) {
            // Se já foi assistido, remove o registro
            $user->watchedWorships()->detach($worship->id);
            $message = 'Adoração desmarcada como assistida.';
        } else {
            // Se não foi assistido, marca como assistido
            $user->watchedWorships()->attach($worship->id, [
                'watched_at' => now(),
            ]);
            $message = 'Adoração marcada como assistida!';
        }

        return back()->with('success', $message);
    }
}
