@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Botão Voltar -->
        <a href="{{ route('worships.index') }}"
            class="inline-flex items-center bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 mb-6 mt-2">
            <!-- Ícone -->
            Voltar para a Listagem
        </a>

        <!-- Card principal -->
        <div class="bg-gray-800 rounded-lg p-6 shadow-lg text-white">
            <h2 class="text-2xl font-semibold mb-4">{{ $worship->title }}</h2>

            <!-- Vídeo -->
            <video controls class="w-full mb-4" poster="{{ $worship->image_url }}" preload="metadata">
                <source src="{{ $worship->video_url }}" type="video/mp4">
                @if ($worship->subtitles)
                    <track
                        kind="subtitles"
                        src="{{ $worship->subtitles['url'] }}"
                        srclang="pt"
                        label="Português"
                        default
                    >
                @endif
                Seu navegador não suporta a reprodução de vídeos.
            </video>

            <!-- Form para marcar como assistida -->
            <form id="markAsWatchedForm-{{ $worship->id }}">
                @csrf
                <button type="button" onclick="markAsWatched({{ $worship->id }})"
                    class="text-sm {{ $worship->wasWatchedBy(Auth::user()) ? 'text-green-500' : 'text-gray-300 hover:text-gray-400' }}">
                    {{ $worship->wasWatchedBy(Auth::user()) ? '✔ Assistida' : 'Marcar como assistida' }}
                </button>
            </form>

            <!-- Botão para abrir o modal e gerar resumo -->
            <div class="mt-6">
                <button id="summarize-button"
                        onclick="openSummaryModal({{ $worship->id }})"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out">
                    Fornecer Resumo
                </button>

            </div>
        </div>
    </div>

    @include('worships.partials.summary-modal')

    <script>
        async function markAsWatched(worshipId) {
            const form = document.getElementById(`markAsWatchedForm-${worshipId}`);
            const url = "{{ route('worship.markAsWatched', ':id') }}".replace(':id', worshipId);
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({})
                });
                const data = await response.json();

                if (data.success) {
                    const button = form.querySelector('button');
                    if (data.action === 'marked') {
                        button.textContent = '✔ Assistida';
                        button.classList.remove('text-gray-300', 'hover:text-gray-400');
                        button.classList.add('text-green-500');
                    } else {
                        button.textContent = 'Marcar como assistida';
                        button.classList.remove('text-green-500');
                        button.classList.add('text-gray-300', 'hover:text-gray-400');
                    }
                }
            } catch (error) {
                console.error('Erro:', error);
            }
        }
    </script>
@endsection
