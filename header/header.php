<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collaborative Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <header class="bg-gradient-to-r from-indigo-600 to-blue-600 p-4 shadow-md">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <!-- Logo SVG : Livre avec du code autour -->
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <!-- Livre -->
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/>
                    <!-- Accolades de code autour -->
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 4c2-2 4-2 6 0M22 4c-2-2-4-2-6 0"/>
                    <!-- Lignes de code -->
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 2h2M17 2h2M5 22h2M17 22h2"/>
                </svg>
                <h1 class="text-2xl font-bold text-white">
                    <a href="/Projet_Web/collaborative_Library/www/index.php" class="hover:text-indigo-200 transition-colors">Collaborative Library</a>
                </h1>
            </div>
            <nav class="space-x-4">
                <a href="/Projet_Web/collaborative_Library/www/index.php" class="text-white hover:bg-indigo-700 px-4 py-2 rounded-lg transition-colors">Accueil</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="text-white hover:bg-indigo-700 px-4 py-2 rounded-lg transition-colors"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
                    <a href="logout.php" class="text-white hover:bg-indigo-700 px-4 py-2 rounded-lg transition-colors">Se déconnecter</a>
                <?php else: ?>
                    <a href="login.php" class="text-white hover:bg-indigo-700 px-4 py-2 rounded-lg transition-colors">Se connecter</a>
                    <a href="register.php" class="bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2 rounded-lg transition-colors font-semibold">S'inscrire</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="max-w-6xl mx-auto mt-6">