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

