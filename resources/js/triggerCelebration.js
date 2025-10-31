function triggerCelebration() {
    // Efeito de confete em tela cheia
    const duration = 3000; // duração em milissegundos
    const end = Date.now() + duration;

    // Configuração para confetes mais festivos
    const colors = [
        "#ff0000",
        "#ffa500",
        "#ffff00",
        "#008000",
        "#0000ff",
        "#4b0082",
        "#ee82ee",
    ];

    (function frame() {
        // Lançar confetes de várias posições
        confetti({
            particleCount: 7,
            angle: 60,
            spread: 55,
            origin: { x: 0 },
            colors: colors,
        });

        confetti({
            particleCount: 7,
            angle: 120,
            spread: 55,
            origin: { x: 1 },
            colors: colors,
        });

        // Lançar de cima para baixo
        confetti({
            particleCount: 10,
            angle: 90,
            spread: 100,
            origin: { y: 0, x: 0.5 },
            colors: colors,
        });

        // Verificar se ainda estamos dentro da duração
        if (Date.now() < end) {
            requestAnimationFrame(frame);
        }
    })();

    const congratsMessage = document.createElement("div");
    congratsMessage.innerHTML =
        "<h2>Parabéns! 🎉</h2><p>Você completou mais uma adoração matinal!</p>";
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
    const style = document.createElement("style");
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

// Exportar para o escopo global para que possa ser chamada de qualquer lugar
// Usando globalThis em vez de window para compatibilidade entre diferentes ambientes JavaScript
globalThis.triggerCelebration = triggerCelebration;
