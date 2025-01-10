@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <!-- Botão para voltar à listagem -->
        <a href="{{ route('worships.index') }}" class="inline-flex items-center bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 mb-6">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Voltar para a Listagem
        </a>

        <!-- Player de Vídeo -->
        <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
            <h2 class="text-xl font-semibold mb-4 text-white">{{ $worship->title }}</h2>
            <video controls class="w-full">
                <source src="{{ $worship->video_url }}" type="video/mp4">
                Seu navegador não suporta a reprodução de vídeos.
            </video>

            <!-- Botão para marcar/desmarcar como assistida -->
            <form id="markAsWatchedForm-{{ $worship->id }}" class="mt-4">
                @csrf
                <button type="button" onclick="markAsWatched({{ $worship->id }})" class="text-sm {{ $worship->wasWatchedBy(Auth::user()) ? 'text-green-500' : 'text-gray-300' }}">
                    {{ $worship->wasWatchedBy(Auth::user()) ? '✔ Assistida' : 'Marcar como assistida' }}
                </button>
            </form>
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
                    // Atualiza o texto do botão
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
    </script>
@endsection
