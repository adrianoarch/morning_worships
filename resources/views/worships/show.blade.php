@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <a href="{{ route('worships.index') }}" class="inline-flex items-center bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 mb-6">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Voltar para a Listagem
        </a>

        <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
            <h2 class="text-xl font-semibold mb-4 text-white">{{ $worship->title }}</h2>
            <video controls class="w-full">
                <source src="{{ $worship->video_url }}" type="video/mp4">
                Seu navegador não suporta a reprodução de vídeos.
            </video>

            <form id="markAsWatchedForm-{{ $worship->id }}" class="mt-4">
                @csrf
                <button type="button" onclick="markAsWatched({{ $worship->id }})" class="text-sm {{ $worship->wasWatchedBy(Auth::user()) ? 'text-green-500' : 'text-gray-300' }}">
                    {{ $worship->wasWatchedBy(Auth::user()) ? '✔ Assistida' : 'Marcar como assistida' }}
                </button>
            </form>

            <div class="mt-6">
                <button id="summarize-button" onclick="getSummary({{ $worship->id }})" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Fornecer Resumo
                </button>
                <div id="summary-area" class="mt-4 p-4 border border-gray-300 rounded hidden">
                    <h3 class="font-semibold mb-2">Resumo da IA:</h3>
                    <div id="summary-text" class="prose prose-sm max-w-none"></div> {{-- Adicionada classe prose para Markdown --}}
                </div>
            </div>
        </div>
    </div>

    <script>
        function markAsWatched(worshipId) {
            const form = document.getElementById(`markAsWatchedForm-${worshipId}`);
            const url = "{{ route('worship.markAsWatched', ':id') }}".replace(':id', worshipId);
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const button = form.querySelector('button');
                    if (data.action === 'marked') {
                        button.textContent = '✔ Assistida';
                        button.classList.remove('text-gray-300');
                        button.classList.add('text-green-500');
                    } else {
                        button.textContent = 'Marcar como assistida';
                        button.classList.remove('text-green-500');
                        button.classList.add('text-gray-300');
                    }
                }
            })
            .catch(error => console.error('Erro:', error));
        }

        function getSummary(worshipId) {
        const summaryButton = document.getElementById('summarize-button');
        const summaryArea = document.getElementById('summary-area');
        const summaryTextElement = document.getElementById('summary-text');

        summaryButton.disabled = true;
        summaryButton.textContent = 'Aguarde...';
        summaryTextElement.textContent = 'Carregando resumo...';
        summaryArea.classList.remove('hidden');
        summaryArea.classList.add('opacity-50', 'animate-pulse');


        fetch(`/worships/${worshipId}/summarize`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erro na requisição: ${response.status} ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            summaryButton.disabled = false;
            summaryButton.textContent = 'Fornecer Resumo';
            summaryArea.classList.remove('opacity-50', 'animate-pulse');

            if (data.summary) {
                // Converter Markdown para HTML usando marked.js e inserir no div
                summaryTextElement.innerHTML = marked.parse(data.summary);
            } else if (data.error) {
                summaryTextElement.textContent = `Erro ao gerar resumo: ${data.error}`;
            }
        })
        .catch(error => {
            summaryButton.disabled = false;
            summaryButton.textContent = 'Fornecer Resumo';
            summaryArea.classList.remove('opacity-50', 'animate-pulse');

            summaryTextElement.textContent = `Erro ao buscar resumo: ${error.message}`;
            summaryArea.classList.remove('hidden');
        });
    }
    </script>
@endsection
