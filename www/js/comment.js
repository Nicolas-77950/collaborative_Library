// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    // Gestionnaire pour afficher/masquer les commentaires
    document.querySelectorAll('.comment-count').forEach(span => {
        span.addEventListener('click', function() {
            const resourceId = this.getAttribute('data-resource-id');
            const commentSection = document.getElementById(`comments-${resourceId}`);
            commentSection.classList.toggle('hidden');
        });
    });

    // Script pour les commentaires (soumission de formulaire)
    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const resourceId = this.getAttribute('data-resource-id');
            const content = this.querySelector('textarea[name="content"]').value.trim();
            const commentList = this.previousElementSibling;
            const commentCountSpan = document.querySelector(`.comment-count[data-resource-id="${resourceId}"]`);

            // Vérifier si l'utilisateur est connecté 
            if (!isLoggedIn) {
                const messageDiv = document.getElementById('login-message');
                messageDiv.textContent = 'Veuillez vous connecter pour commenter.';
                messageDiv.classList.remove('hidden');
                setTimeout(() => {
                    messageDiv.classList.add('hidden');
                }, 5000);
                return;
            }

            // Vérifier si le commentaire est vide
            if (!content) {
                alert('Le commentaire ne peut pas être vide.');
                return;
            }

            // Envoyer une requête AJAX pour ajouter le commentaire
            fetch('add_comment.php', { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `resource_id=${resourceId}&content=${encodeURIComponent(content)}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Erreur HTTP : ${response.status} ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Mettre à jour le nombre de commentaires
                    commentCountSpan.textContent = `${data.comment_count} Commentaires`;

                    // Ajouter le nouveau commentaire à la liste
                    const newComment = document.createElement('div');
                    newComment.classList.add('border-l-4', 'border-indigo-200', 'pl-3');
                    newComment.innerHTML = `
                        <p class="font-semibold text-gray-700">${data.comment.author}</p>
                        <p class="text-gray-600">${data.comment.content}</p>
                    `;
                    commentList.appendChild(newComment);

                    // Supprimer le message "Aucun commentaire" s'il existe
                    const noCommentMessage = commentList.querySelector('.text-gray-500.italic');
                    if (noCommentMessage) {
                        noCommentMessage.remove();
                    }

                    // Vider le formulaire
                    this.querySelector('textarea[name="content"]').value = '';

                    // Afficher la section commentaires après ajout
                    const commentSection = document.getElementById(`comments-${resourceId}`);
                    commentSection.classList.remove('hidden');
                } else {
                    // Afficher un message d'erreur
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de l\'ajout du commentaire : ' + error.message);
            });
        });
    });
});