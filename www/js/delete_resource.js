
let resourceIdToDelete = null;

function showDeleteConfirmation(resourceId) {
    resourceIdToDelete = resourceId;
    const deleteConfirmation = document.getElementById('delete-confirmation');
    deleteConfirmation.classList.remove('hidden');

    // Ajouter des écouteurs d'événements pour les boutons Confirmer et Annuler
    document.getElementById('confirm-delete-btn').onclick = function() {
        window.location.href = 'delete_resource.php?id=' + resourceIdToDelete;
    };
    document.getElementById('cancel-delete-btn').onclick = function() {
        deleteConfirmation.classList.add('hidden');
        resourceIdToDelete = null;
    };

    // Masquer automatiquement le message après 10 secondes si aucune action n'est prise
    setTimeout(() => {
        if (!deleteConfirmation.classList.contains('hidden')) {
            deleteConfirmation.classList.add('hidden');
            resourceIdToDelete = null;
        }
    }, 10000); // 10 secondes
}