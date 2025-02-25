<!-- Modal para exibir o resumo -->
<div id="summary-modal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900 bg-opacity-75 overflow-y-auto">
    <!-- Conteúdo do modal -->
    <div
        class="bg-gray-800 text-white rounded-lg shadow-lg p-6 max-w-2xl w-full mx-4 my-8 relative max-h-[90vh] overflow-y-auto">
        <!-- Botão de fechar (canto superior direito) -->
        <button id="close-modal-button"
            class="absolute top-2 right-2 text-gray-400 hover:text-white text-2xl font-bold sticky">
            &times;
        </button>

        <!-- Título do modal -->
        <h2 class="text-xl font-semibold mt-2 mb-3">Resumo</h2>

        <!-- Spinner (inicialmente escondido) -->
        <div id="spinner" class="flex items-center justify-center mb-4 hidden">
            <svg class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
        </div>

        <!-- Área de texto do resumo -->
        <div id="modal-summary-text" class="prose prose-sm max-w-none text-white mb-4">
            <!-- Conteúdo do resumo será injetado via JS -->
        </div>

        <!-- Botão de copiar resumo -->
        <button id="copy-summary-button"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm flex items-center hidden transition duration-150 ease-in-out mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
            </svg>
            Copiar Resumo
        </button>
    </div>
</div>

<script>
    // Abre o modal e chama a função para buscar o resumo
    function openSummaryModal(worshipId) {
        // Mostrar modal (remover a classe 'hidden', adicionar 'flex')
        const modal = document.getElementById('summary-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Limpa o conteúdo anterior do resumo
        const summaryText = document.getElementById('modal-summary-text');
        summaryText.innerHTML = '';

        // Esconde o botão de copiar e mostra o spinner
        document.getElementById('copy-summary-button').classList.add('hidden');
        document.getElementById('spinner').classList.remove('hidden');

        // Desabilita temporariamente o botão que abre o modal, para evitar cliques múltiplos
        const summarizeButton = document.getElementById('summarize-button');
        summarizeButton.disabled = true;

        // Chama a função que busca o resumo
        getSummary(worshipId).then(() => {
            summarizeButton.disabled = false;
        });
    }

    // Fecha o modal
    document.getElementById('close-modal-button').addEventListener('click', () => {
        const modal = document.getElementById('summary-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });

    // Função para buscar o resumo (fetch)
    async function getSummary(worshipId) {
        try {
            const response = await fetch(`/worships/${worshipId}/summarize`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            });

            if (!response.ok) {
                throw new Error(`Erro na requisição: ${response.status} ${response.statusText}`);
            }

            const data = await response.json();

            // Esconde o spinner ao receber a resposta
            document.getElementById('spinner').classList.add('hidden');

            if (data.summary) {
                // Converter Markdown para HTML usando a biblioteca "marked" (se estiver usando)
                document.getElementById('modal-summary-text').innerHTML = marked.parse(data.summary);
                // Exibe botão de cópia
                document.getElementById('copy-summary-button').classList.remove('hidden');
            } else if (data.error) {
                document.getElementById('modal-summary-text').textContent = `Erro ao gerar resumo: ${data.error}`;
            }
        } catch (error) {
            document.getElementById('spinner').classList.add('hidden');
            document.getElementById('modal-summary-text').textContent = `Erro ao buscar resumo: ${error.message}`;
        }
    }

    // Copiar resumo para área de transferência
    document.getElementById('copy-summary-button').addEventListener('click', function() {
        const summaryText = document.getElementById('modal-summary-text').innerText;

        const tempElement = document.createElement('textarea');
        tempElement.value = summaryText;
        document.body.appendChild(tempElement);
        tempElement.select();

        try {
            document.execCommand('copy');
            // Feedback visual
            const originalHTML = this.innerHTML;
            this.innerHTML =
                '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Copiado!';
            this.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            this.classList.add('bg-green-600', 'hover:bg-green-700');

            setTimeout(() => {
                this.innerHTML = originalHTML;
                this.classList.remove('bg-green-600', 'hover:bg-green-700');
                this.classList.add('bg-blue-600', 'hover:bg-blue-700');
            }, 2000);
        } catch (err) {
            console.error('Erro ao copiar texto: ', err);
        }

        document.body.removeChild(tempElement);
    });
</script>
