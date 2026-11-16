document.addEventListener('DOMContentLoaded', () => {
    // 1. Chat en tiempo real
    const chatBox = document.getElementById('chatBox');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');

    if (chatBox && chatForm) {
        const intercambioId = chatBox.dataset.intercambio;
        const miUid = chatBox.dataset.uid;

        const cargarMensajes = async () => {
            const res = await fetch(`acciones.php?accion=chat_leer&id=${intercambioId}`);
            const mensajes = await res.json();
            chatBox.innerHTML = mensajes.map(m => `
                <div class="msg ${m.remitente_id == miUid ? 'sent' : 'received'}">
                    <div>${m.contenido}</div>
                </div>
            `).join('');
            chatBox.scrollTop = chatBox.scrollHeight;
        };

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const texto = chatInput.value.trim();
            if (!texto) return;

            await fetch('acciones.php?accion=chat_enviar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ intercambio_id: intercambioId, contenido: texto })
            });
            chatInput.value = '';
            cargarMensajes();
        });

        cargarMensajes();
        setInterval(cargarMensajes, 3000);
    }

    // 2. Modal de calificación con estrellas
    const modalEval = document.getElementById('modalEvaluacion');
    const btnCerrar = document.getElementById('btnCerrarModal');

    if (modalEval) {
        document.querySelectorAll('.btn-abrir-eval').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('evalIntercambioId').value = btn.dataset.intercambio;
                document.getElementById('evalEvaluadoId').value = btn.dataset.evaluado;
                document.getElementById('evalSubtitulo').textContent = `¿Cómo fue tu experiencia con ${btn.dataset.nombre}?`;
                modalEval.classList.add('active');
            });
        });
        btnCerrar.addEventListener('click', () => modalEval.classList.remove('active'));
        modalEval.addEventListener('click', (e) => { if (e.target === modalEval) modalEval.classList.remove('active'); });
    }
});