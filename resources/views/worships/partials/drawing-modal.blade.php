<!-- Modal para exibir o desenho gerado -->
<div id="drawing-modal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900 bg-opacity-75 overflow-y-auto">
    <!-- Conteúdo do modal -->
    <div
        class="bg-gray-800 text-white rounded-lg shadow-lg p-6 max-w-2xl w-full mx-4 my-8 relative max-h-[90vh] overflow-y-auto">
        <!-- Botão de fechar (canto superior direito) -->
        <button id="close-drawing-modal-button"
            class="absolute top-2 right-2 text-gray-400 hover:text-white text-2xl font-bold sticky">
            &times;
        </button>

        <!-- Título do modal -->
        <h2 class="text-xl font-semibold mt-2 mb-3">Desenho Gerado</h2>

        <!-- Spinner com mensagem de carregamento -->
        <div id="drawing-spinner" class="flex flex-col items-center justify-center mb-4 hidden">
            <svg class="animate-spin h-8 w-8 text-white mb-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <p class="text-gray-300 text-sm">Gerando o desenho. Aguarde...</p>
        </div>

        <!-- Área para exibir a imagem gerada -->
        <div id="drawing-image-container" class="flex justify-center mb-4 hidden">
            <img id="drawing-image" class="max-w-full rounded-lg shadow-md" alt="Desenho gerado pela IA">
        </div>

        <!-- Mensagem de erro -->
        <div id="drawing-error-text" class="text-red-400 text-center mb-4 hidden"></div>

        <!-- Botão de download -->
        <button id="download-drawing-button"
            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm flex items-center hidden transition duration-150 ease-in-out mx-auto">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Download
        </button>
    </div>
</div>

<script>
    // Abre o modal e chama a função para gerar o desenho
    function openDrawingModal(worshipId) {
        const modal = document.getElementById('drawing-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Resetar estado do modal
        document.getElementById('drawing-image-container').classList.add('hidden');
        document.getElementById('drawing-error-text').classList.add('hidden');
        document.getElementById('download-drawing-button').classList.add('hidden');
        document.getElementById('drawing-spinner').classList.remove('hidden');
        document.getElementById('drawing-image').src = '';
        document.getElementById('drawing-error-text').textContent = '';

        // Desabilita o botão para evitar cliques múltiplos
        const drawingButton = document.getElementById('generate-drawing-button');
        drawingButton.disabled = true;

        // Chama a função que gera o desenho
        getDrawing(worshipId).then(() => {
            drawingButton.disabled = false;
        });
    }

    // Fecha o modal
    document.getElementById('close-drawing-modal-button').addEventListener('click', () => {
        const modal = document.getElementById('drawing-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });

    // Função para gerar o desenho (fetch)
    async function getDrawing(worshipId) {
        try {
            const response = await fetch(`/worships/${worshipId}/generate-drawing`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            });

            const data = await response.json();

            // Esconde o spinner
            document.getElementById('drawing-spinner').classList.add('hidden');

            if (!response.ok) {
                throw new Error(data.error || `Erro na requisição: ${response.status} ${response.statusText}`);
            }

            if (data.success && data.image) {
                // Exibir a imagem gerada
                const img = document.getElementById('drawing-image');
                img.src = data.image;
                document.getElementById('drawing-image-container').classList.remove('hidden');

                // Exibir o botão de download
                document.getElementById('download-drawing-button').classList.remove('hidden');
            } else if (data.error) {
                document.getElementById('drawing-error-text').textContent = data.error;
                document.getElementById('drawing-error-text').classList.remove('hidden');
            }
        } catch (error) {
            document.getElementById('drawing-spinner').classList.add('hidden');
            document.getElementById('drawing-error-text').textContent = `Erro ao gerar desenho: ${error.message}`;
            document.getElementById('drawing-error-text').classList.remove('hidden');
        }
    }

    // Download do desenho gerado
    document.getElementById('download-drawing-button').addEventListener('click', function() {
        const img = document.getElementById('drawing-image');
        const dataUri = img.src;

        if (!dataUri || !dataUri.startsWith('data:')) {
            return;
        }

        const link = document.createElement('a');
        link.href = dataUri;

        // Extrair a extensão do mimeType
        const mimeMatch = dataUri.match(/^data:(image\/\w+);/);
        const ext = mimeMatch ? mimeMatch[1].split('/')[1] : 'png';

        link.download = `desenho_chibi.${ext}`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
</script>
