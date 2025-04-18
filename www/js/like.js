// Script pour les likes
document.querySelectorAll('.like-btn').forEach(button => {
    button.addEventListener('click', function() {
        const resourceId = this.getAttribute('data-resource-id');
        const likeCountSpan = this.querySelector('.like-count');
        const heartIcon = this.querySelector('svg');

        // Vérifier si l'utilisateur est connecté
        if (!isLoggedIn) {
            const messageDiv = document.getElementById('login-message');
            messageDiv.textContent = 'Veuillez vous connecter pour liker une ressource.';
            messageDiv.classList.remove('hidden');
            setTimeout(() => {
                messageDiv.classList.add('hidden');
            }, 5000);
            return;
        }

        // Envoyer une requête AJAX pour liker ou déliker la ressource
        fetch('like_resource.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `resource_id=${resourceId}`
        })
        .then(response => {
            // Vérifier si la réponse est OK (code 200)
            if (!response.ok) {
                throw new Error(`Erreur HTTP : ${response.status} ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Mettre à jour le nombre de likes
                likeCountSpan.textContent = `${data.like_count} Likes`;

                // Mettre à jour l'apparence du bouton en fonction de l'état
                if (data.has_liked) {
                    // L'utilisateur vient de liker
                    heartIcon.classList.remove('text-gray-600');
                    heartIcon.classList.add('text-red-500');
                    heartIcon.setAttribute('fill', 'currentColor');
                } else {
                    // L'utilisateur vient de déliker
                    heartIcon.classList.remove('text-red-500');
                    heartIcon.classList.add('text-gray-600');
                    heartIcon.setAttribute('fill', 'none');
                }
            } else {
                // Afficher un message d'erreur
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors du like : ' + error.message);
        });
    });
});