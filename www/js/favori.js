document.addEventListener('DOMContentLoaded', () => {
    const favoriButtons = document.querySelectorAll('.favori-btn');
    const favoriLoginMessage = document.getElementById('favori-login-message');

    favoriButtons.forEach(button => {
        button.addEventListener('click', async () => {
            // Réinitialiser le message précédent (le masquer)
            favoriLoginMessage.classList.add('hidden');

            if (!isLoggedIn) {
                // Afficher le message dans le conteneur
                favoriLoginMessage.classList.remove('hidden');
                return;
            }

            const resourceId = button.getAttribute('data-resource-id');
            const isFavorited = button.getAttribute('data-is-favorited') === 'true';
            const favoriCountSpan = button.querySelector('.favori-count');
            const svg = button.querySelector('svg');

            try {
                const response = await fetch('toggle_favori.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `resource_id=${resourceId}&action=${isFavorited ? 'remove' : 'add'}`
                });

                const data = await response.json();

                if (data.success) {
                    // Mettre à jour l'état du bouton
                    button.setAttribute('data-is-favorited', data.is_favorited ? 'true' : 'false');
                    svg.classList.toggle('text-yellow-500', data.is_favorited);
                    svg.classList.toggle('text-gray-600', !data.is_favorited);
                    svg.setAttribute('fill', data.is_favorited ? 'currentColor' : 'none');
                    favoriCountSpan.textContent = `${data.favori_count} Favoris`;
                } else {
                    // Afficher un message d'erreur dans le même conteneur
                    favoriLoginMessage.textContent = data.message || 'Une erreur est survenue.';
                    favoriLoginMessage.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Erreur lors de la gestion des favoris:', error);
                favoriLoginMessage.textContent = 'Une erreur est survenue.';
                favoriLoginMessage.classList.remove('hidden');
            }
        });
    });
});