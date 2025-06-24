<div class="comentarios-section" id="comentarios-container">
    <div class="comments-header">
        <i class="fas fa-comments"></i>
        <h2>Comentarios</h2>
        <div class="total-badge" id="comentarios-count">
            <strong>0</strong>
        </div>
    </div>

    @auth
    <div class="comment-form">
        <form id="form-comentario">
            <textarea name="opinion" id="opinion" placeholder="Escribe un comentario..." required></textarea>
            <button type="submit" class="send-button">
                <i class="fas fa-paper-plane"></i>
                Enviar
            </button>
            <div style="clear: both;"></div>
        </form>
    </div>
    @else
    <div class="login-prompt">
        <p>Inicia sesión para dejar un comentario</p>
        <a href="{{ route('login') }}" class="btn-login">Iniciar sesión</a>
    </div>
    @endauth

    <div class="comentarios-list" id="comentarios-list"> 
    </div>

    <div class="empty-state" id="no-comentarios" style="display: none;">
        <i class="fas fa-comment-slash"></i>
        <p>No hay comentarios. ¡Sé el primero en comentar!</p>
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', function() {
    const obraId = {{ $obra->id }};
    const comentariosList = document.getElementById('comentarios-list');
    const comentariosCount = document.getElementById('comentarios-count').querySelector('strong');
    const noComentarios = document.getElementById('no-comentarios');
    const formComentario = document.getElementById('form-comentario');
     
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
     
    const urlGetComentarios = "{{ route('opiniones.get', ['obra_id' => $obra->id]) }}";
    const urlStoreComentario = "{{ route('opiniones.store', ['obra_id' => $obra->id]) }}";
    const urlResponderBase = "{{ url('/obras') }}/";
     
    const comentariosPorPagina = 10;
    let paginaActual = 1;
    let todosLosComentarios = [];

    const isAdmin = @json(auth()->check() && auth()->user()->role === 'admin');

    function cargarComentarios() {
        const loadingIndicator = document.createElement('div');
        loadingIndicator.className = 'loading-indicator';
        loadingIndicator.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cargando comentarios...';
        loadingIndicator.style.textAlign = 'center';
        loadingIndicator.style.padding = '20px';
        loadingIndicator.style.color = 'var(--text-secondary)';
        
        comentariosList.innerHTML = '';
        comentariosList.appendChild(loadingIndicator);
        
        fetch(urlGetComentarios)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al cargar comentarios');
                }
                return response.json();
            })
            .then(data => { 
                comentariosList.innerHTML = '';
                comentariosCount.textContent = data.length;
                todosLosComentarios = data;
                paginaActual = 1;
                
                if (data.length === 0) {
                    noComentarios.style.display = 'block';
                } else {
                    noComentarios.style.display = 'none';
                    mostrarComentarios();
                }
            })
            .catch(error => console.error('Error al cargar comentarios:', error));
    }
    
    function mostrarComentarios() { 
        if (paginaActual === 1) {
            comentariosList.innerHTML = '';
        } else { 
            const verMasBtn = comentariosList.querySelector('.ver-mas-btn');
            if (verMasBtn) {
                verMasBtn.remove();
            }
        }
        
        const inicio = (paginaActual - 1) * comentariosPorPagina;
        const fin = Math.min(paginaActual * comentariosPorPagina, todosLosComentarios.length);
        
        for (let i = inicio; i < fin; i++) {
            const comentario = todosLosComentarios[i];
            const comentarioEl = document.createElement('div');
            comentarioEl.className = 'comment-item';
            comentarioEl.id = `comment-${comentario.id}`;
            
            let respuestaHTML = ''; 
            if (comentario.respuesta && comentario.admin) {
                respuestaHTML = `
                    <div class="admin-reply">
                        <div class="reply-author">
                            <i class="fas fa-user-shield"></i>
                            ${comentario.admin.name} (Admin)
                        </div>
                        <div class="reply-content">${comentario.respuesta}</div>
                    </div>
                `;
            } 
            let replyButtonHTML = '';
            let replyFormHTML = '';
            
            if (isAdmin) {
                replyButtonHTML = `
                    <button class="reply-button" data-comment-id="${comentario.id}">
                        <i class="fas fa-reply"></i>
                        Responder
                    </button>
                `;
                
                replyFormHTML = `
                    <div class="reply-form" id="reply-form-${comentario.id}" style="display:none;">
                        <textarea placeholder="Escribe una respuesta..."></textarea>
                        <button class="send-reply-button" data-send-comment-id="${comentario.id}">
                            <i class="fas fa-paper-plane"></i>
                            Enviar
                        </button>
                        <div style="clear: both;"></div>
                    </div>
                `;
            }
            
            comentarioEl.innerHTML = `
                <div class="comment-header" style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <div class="comment-user">${comentario.user.name}</div>
                        <div class="comment-time">${comentario.created_at}</div>
                    </div>
                    <div class="comment-replies-count" style="font-size: 0.8rem; color: var(--text-secondary); background-color: var(--bg-secondary); padding: 2px 8px; border-radius: 12px;">
                        <i class="fas fa-reply"></i> ${comentario.respuestas_count}
                    </div>
                </div>
                <div class="comment-content">${comentario.opinion}</div>
                <div class="comment-actions"> 
                    ${replyButtonHTML}
                </div>
                ${replyFormHTML}
                ${respuestaHTML}
            `;
            
            comentariosList.appendChild(comentarioEl);
        } 
        if (todosLosComentarios.length > paginaActual * comentariosPorPagina) {
            const verMasBtn = document.createElement('button');
            verMasBtn.className = 'ver-mas-btn';
            verMasBtn.innerHTML = '<i class="fas fa-chevron-down"></i> Ver más comentarios';
            verMasBtn.style.backgroundColor = 'var(--bg-secondary)';
            verMasBtn.style.color = 'var(--text-primary)';
            verMasBtn.style.border = '1px solid var(--border-color)';
            verMasBtn.style.borderRadius = '6px';
            verMasBtn.style.padding = '10px 15px';
            verMasBtn.style.margin = '15px auto';
            verMasBtn.style.display = 'block';
            verMasBtn.style.cursor = 'pointer';
            verMasBtn.style.width = '200px';
            verMasBtn.style.transition = 'all 0.3s ease';
            
            verMasBtn.addEventListener('mouseover', function() {
                this.style.backgroundColor = 'var(--bg-hover)';
            });
            
            verMasBtn.addEventListener('mouseout', function() {
                this.style.backgroundColor = 'var(--bg-secondary)';
            });
            
            verMasBtn.addEventListener('click', function() {
                paginaActual++;
                mostrarComentarios();
            });
            
            comentariosList.appendChild(verMasBtn);
        }
        
        const replyButtons = document.querySelectorAll('.reply-button');
        replyButtons.forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.getAttribute('data-comment-id');
                const replyForm = document.getElementById(`reply-form-${commentId}`);
                
                document.querySelectorAll('.reply-form').forEach(form => {
                    if (form.id !== `reply-form-${commentId}`) {
                        form.style.display = 'none';
                    }
                });
                
                if (replyForm.style.display === 'block') {
                    replyForm.style.display = 'none';
                } else {
                    replyForm.style.display = 'block';
                }
            });
        });
        
        const sendReplyButtons = document.querySelectorAll('.send-reply-button');
        sendReplyButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.stopPropagation(); // Evita burbujas accidentales
                const commentId = this.getAttribute('data-send-comment-id');
                const replyForm = document.getElementById(`reply-form-${commentId}`);
                if (!replyForm) {
                    console.error('No se encontró el formulario de respuesta para el comentario:', commentId);
                    return;
                }
                const replyTextarea = replyForm.querySelector('textarea');
                if (!replyTextarea) {
                    console.error('No se encontró el textarea para el comentario:', commentId);
                    return;
                }
                const replyText = replyTextarea.value;
                const sendButton = this;
 
                console.log('Botón Enviar respuesta clickeado:', { commentId, replyForm, replyTextarea, replyText });

                if (replyText.trim() === '') {
                    replyTextarea.style.borderColor = '#ff4d4f';
                    replyTextarea.focus();
                    setTimeout(() => {
                        replyTextarea.style.borderColor = '';
                    }, 3000);
                    return;
                }

                const originalButtonText = sendButton.innerHTML;
                sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
                sendButton.disabled = true;

                const obraId = {{ $obra->id }};
                console.log('Enviando respuesta a:', `${urlResponderBase}${commentId}`);
                console.log('Datos enviados:', { opinion_id: commentId, respuesta: replyText });

                fetch(`${urlResponderBase}${obraId}/opiniones/${commentId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ respuesta: replyText })
                })
                .then(response => {
                    console.log('Respuesta del servidor:', response.status);
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Respuesta recibida:', data);
 
                    const comentarioEl = document.getElementById(`comment-${commentId}`);
                    if (comentarioEl) { 
                        const respuestaEl = document.createElement('div');
                        respuestaEl.className = 'admin-reply';

                        respuestaEl.innerHTML = `
                            <div class="reply-author">
                                <i class="fas fa-user-shield"></i>
                                ${data.admin.name} (Admin)
                            </div>
                            <div class="reply-content">${data.respuesta}</div>
                        `;
 
                        const existingReply = comentarioEl.querySelector('.admin-reply');
                        if (existingReply) {
                            existingReply.remove();
                        }
 
                        comentarioEl.appendChild(respuestaEl);
 
                        const repliesCount = comentarioEl.querySelector('.comment-replies-count');
                        if (repliesCount) {
                            repliesCount.innerHTML = '<i class="fas fa-reply"></i> 1';
                        }
 
                        replyForm.style.display = 'none';
                        replyTextarea.value = '';
                    }
 
                    setTimeout(() => {
                        cargarComentarios();
                    }, 1000);

                    sendButton.innerHTML = originalButtonText;
                    sendButton.disabled = false;
                })
                .catch(error => {
                    console.error('Error al enviar respuesta:', error);

                    const errorMsg = document.createElement('div');
                    errorMsg.className = 'error-message';
                    errorMsg.textContent = 'Error al enviar la respuesta. Intente nuevamente.';
                    errorMsg.style.color = '#ff4d4f';
                    errorMsg.style.marginTop = '10px';
                    errorMsg.style.fontSize = '0.9rem';
                    errorMsg.style.padding = '8px';
                    errorMsg.style.backgroundColor = 'rgba(255, 77, 79, 0.1)';
                    errorMsg.style.borderRadius = '6px';

                    const existingError = replyForm.querySelector('.error-message');
                    if (existingError) {
                        existingError.remove();
                    }

                    replyForm.appendChild(errorMsg);

                    setTimeout(() => {
                        errorMsg.remove();
                    }, 5000);

                    sendButton.innerHTML = originalButtonText;
                    sendButton.disabled = false;
                });
            });
        });
    }
    
    if (formComentario) {
        formComentario.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const opinion = document.getElementById('opinion').value;
            if (!opinion.trim()) return;
            
            fetch(urlStoreComentario, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ opinion })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('opinion').value = '';
                
                const successMsg = document.createElement('div');
                successMsg.className = 'success-message';
                successMsg.textContent = 'Comentario enviado correctamente';
                successMsg.style.color = '#52c41a';
                successMsg.style.padding = '10px';
                successMsg.style.marginTop = '10px';
                successMsg.style.borderRadius = '6px';
                successMsg.style.backgroundColor = 'rgba(82, 196, 26, 0.1)';
                successMsg.style.textAlign = 'center';
                
                formComentario.appendChild(successMsg);
                
                setTimeout(() => {
                    successMsg.remove();
                    cargarComentarios();
                }, 2000);
            })
            .catch(error => {
                console.error('Error al enviar comentario:', error);
                
                const errorMsg = document.createElement('div');
                errorMsg.className = 'error-message';
                errorMsg.textContent = 'Error al enviar el comentario. Intente nuevamente.';
                errorMsg.style.color = '#ff4d4f';
                errorMsg.style.marginTop = '10px';
                errorMsg.style.fontSize = '0.9rem';
                errorMsg.style.padding = '8px';
                errorMsg.style.backgroundColor = 'rgba(255, 77, 79, 0.1)';
                errorMsg.style.borderRadius = '6px';
                
                const existingError = formComentario.querySelector('.error-message');
                if (existingError) {
                    existingError.remove();
                }
                
                formComentario.appendChild(errorMsg);
                
                setTimeout(() => {
                    errorMsg.remove();
                }, 5000);
            });
        });
    }
    
    cargarComentarios();
    setInterval(cargarComentarios, 30000);
});
</script>
          