@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-2">
        <h1 class="text-3xl font-bold mb-6 text-white">Adorações Matinais</h1>

        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
            <!-- Texto de adorações assistidas -->
            <p class="text-lg text-gray-300">
                Adorações assistidas: <span id="watchedCount">{{ $watchedWorshipsCount }}</span> de {{ $worships->total() }}
            </p>

            <!-- Formulário de busca -->
            <form action="{{ route('worships.index') }}" method="GET"
                class="w-full sm:w-auto flex flex-col sm:flex-row sm:items-center sm:justify-end gap-6"
                x-data="searchForm" x-on:submit.prevent="submitForm" x-ref="searchForm">
                <input type="hidden" id="watched" name="watched" x-model.number="watched">
                <button type="button" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                    :class="{ 'bg-blue-700': watched }" @click="watched = true; submitForm()">
                    Exibir adorações assistidas
                </button>

                @php
                    $playlistQuery = array_merge(request()->query(), ['playlist' => 1]);
                @endphp
                <a href="{{ $firstWorshipId ? route('worship.show', array_merge(['worship' => $firstWorshipId], $playlistQuery)) : '#' }}"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 disabled:opacity-50 disabled:pointer-events-none text-center"
                    @class(['pointer-events-none opacity-50' => !$firstWorshipId])>
                    Assistir todos
                </a>

                <!-- Campo de busca (input + lupa/loading) -->
                <div class="flex w-full sm:w-64 gap-2">
                    <input type="text" name="search" x-model="searchQuery" @keydown.enter.prevent="submitForm"
                        class="flex-1 pl-4 py-2 rounded-lg bg-gray-700 border border-gray-600
                               focus:border-blue-500 focus:ring-blue-500 focus:outline-none text-white
                               placeholder-gray-400"
                        x-bind:placeholder="searchInSubtitles ? 'Buscar nas legendas...' : 'Buscar adoração...'">

                    <!-- Botão de busca (ícone de lupa) -->
                    <button type="submit"
                        class="px-2 py-2 rounded-lg bg-gray-700 border border-gray-600 text-gray-400
                               hover:text-white hover:bg-gray-600 transition-colors duration-200"
                        x-show="!isLoading">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>

                <!-- Botão "Limpar" (destacado) -->
                <button type="button" x-show="searchQuery || watched" x-on:click="clearSearch" x-cloak
                    @click="watched = false"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                    Limpar
                </button>

                <!-- Checkbox "Buscar no conteúdo das legendas" -->
                <label for="search_in_subtitles" class="flex items-center gap-2">
                    <input type="checkbox" name="search_in_subtitles" id="search_in_subtitles" x-model="searchInSubtitles"
                        class="rounded bg-gray-700 border-gray-600 text-blue-500 focus:ring-blue-500"
                        {{ request('search_in_subtitles') ? 'checked' : '' }}>
                    <span class="text-sm text-gray-300">Buscar no conteúdo das legendas</span>
                </label>

                <!-- Checkbox "Busca exata" (só aparece quando busca nas legendas está ativa) -->
                <label for="exact_phrase" class="flex items-center gap-2" x-show="searchInSubtitles" x-cloak>
                    <input type="checkbox" name="exact_phrase" id="exact_phrase" x-model="exactPhrase"
                        class="rounded bg-gray-700 border-gray-600 text-blue-500 focus:ring-blue-500"
                        {{ request('exact_phrase') ? 'checked' : '' }}>
                    <span class="text-sm text-gray-300">Busca exata (frase completa)</span>
                </label>
            </form>
        </div>


        @if ($worships->isEmpty())
            <p class="text-lg text-gray-300">Nenhuma adoração encontrada.</p>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($worships as $worship)
                <div x-data="{ open: false }" :class="{ 'pointer-events-none': open }"
                    class="bg-gray-700 p-6 rounded-lg shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                    <!-- Imagem da capa -->
                    @if ($worship->image_url)
                        <img src="{{ $worship->image_url }}" alt="{{ $worship->title }}"
                            class="w-full h-48 object-cover rounded-lg mb-4">
                    @endif

                    <h2 class="text-xl font-semibold mb-2 text-white">{{ $worship->title }}</h2>
                    <p class="text-gray-300 mb-4">{{ $worship->description }}</p>
                    <p class="text-sm text-gray-400 mb-2">
                        Publicado em: {{ $worship->first_published->format('d/m/Y H:i') }}
                    </p>
                    <p class="text-sm text-gray-400 mb-4">
                        Duração: {{ $worship->duration_formatted }}
                    </p>

                    <a href="{{ route('worship.show', $worship->id) }}"
                        class="inline-flex items-center bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Assistir
                    </a>

                    <!-- Botão para marcar/desmarcar como assistida -->
                    <form id="markAsWatchedForm-{{ $worship->id }}" class="mt-4">
                        @csrf
                        <button type="button" onclick="markAsWatched({{ $worship->id }})"
                            class="text-sm {{ $worship->wasWatchedBy(Auth::user()) ? 'text-green-500' : 'text-gray-300 mark-as-watched' }}">
                            {{ $worship->wasWatchedBy(Auth::user()) ? '✔ Assistida' : 'Marcar como assistida' }}
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <!-- Paginação -->
        <div class="mt-8 pb-2">
            {{ $worships->appends(request()->query())->links() }}
        </div>
    </div>

    <script>
        function triggerCelebration() {
            // Efeito de confete em tela cheia
            const duration = 3000; // duração em milissegundos
            const end = Date.now() + duration;

            // Configuração para confetes mais festivos
            const colors = ['#ff0000', '#ffa500', '#ffff00', '#008000', '#0000ff', '#4b0082', '#ee82ee'];

            (function frame() {
                // Lançar confetes de várias posições
                confetti({
                    particleCount: 7,
                    angle: 60,
                    spread: 55,
                    origin: {
                        x: 0
                    },
                    colors: colors
                });

                confetti({
                    particleCount: 7,
                    angle: 120,
                    spread: 55,
                    origin: {
                        x: 1
                    },
                    colors: colors
                });

                // Lançar de cima para baixo
                confetti({
                    particleCount: 10,
                    angle: 90,
                    spread: 100,
                    origin: {
                        y: 0,
                        x: 0.5
                    },
                    colors: colors
                });

                // Verificar se ainda estamos dentro da duração
                if (Date.now() < end) {
                    requestAnimationFrame(frame);
                }
            }());

            const congratsMessage = document.createElement('div');
            congratsMessage.innerHTML = '<h2>Parabéns! 🎉</h2><p>Você completou mais uma adoração matinal!</p>';
            congratsMessage.style.cssText = `
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background-color: rgba(255, 255, 255, 0.9);
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
                z-index: 1000;
                text-align: center;
                animation: fadeInOut 3s forwards;
                color: #333;
            `;

            // Adicionar estilo de animação para a mensagem
            const style = document.createElement('style');
            style.innerHTML = `
                @keyframes fadeInOut {
                    0% { opacity: 0; }
                    20% { opacity: 1; }
                    80% { opacity: 1; }
                    100% { opacity: 0; }
                }
            `;
            document.head.appendChild(style);

            // Adicionar a mensagem ao corpo do documento
            document.body.appendChild(congratsMessage);

            // Remover a mensagem após a duração do efeito
            setTimeout(() => {
                congratsMessage.remove();
            }, duration);
        }

        function markAsWatched(worshipId) {
            const form = document.getElementById(`markAsWatchedForm-${worshipId}`);
            const button = form.querySelector('button');
            const isWatched = button.textContent.trim() === '✔ Assistida';

            // Define a URL com base no estado atual
            const url = isWatched ?
                "{{ route('worship.markAsUnwatched', ':id') }}".replace(':id', worshipId) :
                "{{ route('worship.markAsWatched', ':id') }}".replace(':id', worshipId);

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
                        // Atualiza o texto e a cor do botão
                        if (data.action === 'marked') {
                            button.textContent = '✔ Assistida';
                            button.classList.remove('text-gray-300');
                            button.classList.remove('mark-as-watched');
                            button.classList.add('text-green-500');

                            triggerCelebration();
                        } else {
                            button.textContent = 'Marcar como assistida';
                            button.classList.remove('text-green-500');
                            button.classList.add('text-gray-300');
                            button.classList.add('mark-as-watched');
                        }

                        // Atualiza o contador de adorações assistidas
                        const watchedCountElement = document.getElementById('watchedCount');
                        watchedCountElement.textContent = data.watchedCount;
                    }
                })
                .catch(error => console.error('Erro:', error));
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('searchForm', () => ({
                isLoading: false,
                searchQuery: '{{ request('search', '') }}',
                searchInSubtitles: {{ request('search_in_subtitles', 'false') === 'true' ? 'true' : 'false' }},
                watched: {{ request('watched', 'false') === 'true' ? 'true' : 'false' }},
                exactPhrase: {{ request('exact_phrase', 'false') === 'true' ? 'true' : 'false' }},

                submitForm() {
                    this.isLoading = true;
                    // Aguarda o flush do x-model (p.ex., watched=true) antes de submeter
                    this.$nextTick(() => {
                        this.$refs.searchForm.submit();
                    });
                },

                clearSearch() {
                    this.searchQuery = '';
                    this.searchInSubtitles = false;
                    this.watched = false;
                    this.exactPhrase = false;
                    // Remove os parâmetros da URL sem recarregar a página
                    window.history.pushState({}, '', window.location.pathname);
                    // Recarrega a página sem os parâmetros
                    window.location.reload();
                },

                // Previnir ação do botão de enter para não recarregar a página
                preventEnter(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                    }
                }
            }));
        });
    </script>
@endsection
