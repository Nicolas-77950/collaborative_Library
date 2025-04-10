<?php
// Démarrer la session
session_start();


// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Collaborative Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Inclure le header -->
    <?php require_once '../header/header.php'; ?>


    <main class="max-w-6xl mx-auto mt-8 p-6">
        <div class="max-w-md mx-auto bg-white rounded-xl shadow-lg p-8">
            <h1 class="text-2xl font-bold text-indigo-700 text-center mb-6">Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?> !</h1>
            <p class="text-center text-gray-600">Ceci est votre page de profil.</p>
        </div>
    </main>


    <!-- Inclure le footer -->
    <?php include '../footer/footer.php'; ?>
</body>
</html>
