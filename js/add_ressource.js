
document.getElementById('add-resource-btn').addEventListener('click', function() {
if ($isLoggedIn) {
    // Si l'utilisateur est connecté, rediriger vers add_resource.php
    window.location.href = 'add_resource.php';
} else {
    // Si l'utilisateur n'est pas connecté, afficher le message pendant 5 secondes
    const messageDiv = document.getElementById('login-message');
    messageDiv.classList.remove('hidden');
    setTimeout(() => {
        messageDiv.classList.add('hidden');
    }, 5000);
}
});
