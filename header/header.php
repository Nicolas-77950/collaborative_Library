<?php
// Déterminer la page actuelle
$current_page = basename($_SERVER['PHP_SELF']);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collaborative Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <header class="bg-gradient-to-r from-indigo-700 to-blue-600 p-4 shadow-lg">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <!-- Logo et titre -->
            <div class="flex items-center space-x-3 sm:space-x-4">
                <!-- Logo SVG : Livre avec du code autour -->
                <svg class="w-10 h-10 sm:w-12 sm:h-12 text-white transform hover:scale-105 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 4c2-2 4-2 6 0M22 4c-2-2-4-2-6 0"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 2h2M17 2h2M5 22h2M17 22h2"/>
                </svg>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                    <a href="/Projet_Web/collaborative_Library/www/index.php" class="hover:text-indigo-200 transition-colors duration-300">Collaborative Library</a>
                </h1>
            </div>


            <!-- Navigation pour desktop -->
            <nav class="hidden md:flex items-center space-x-4 lg:space-x-6">
                <a href="/Projet_Web/collaborative_Library/www/index.php"
                   class="px-4 py-2 rounded-full font-medium transition-all duration-300 <?php echo $current_page === 'index.php' ? 'bg-white text-indigo-700 shadow-md' : 'text-white hover:bg-indigo-800 hover:shadow-md'; ?>">
                    Accueil
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php"
                       class="px-4 py-2 rounded-full font-medium transition-all duration-300 flex items-center space-x-2 <?php echo $current_page === 'profile.php' ? 'bg-white text-indigo-700 shadow-md' : 'text-white hover:bg-indigo-800 hover:shadow-md'; ?>">
                        <!-- Icône utilisateur -->
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 <?php echo $current_page === 'profile.php' ? 'text-indigo-700' : 'text-white'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </a>
                    <a href="logout.php"
                       class="px-4 py-2 rounded-full font-medium transition-all duration-300 flex items-center space-x-2 <?php echo $current_page === 'logout.php' ? 'bg-white text-indigo-700 shadow-md' : 'text-white hover:bg-indigo-800 hover:shadow-md'; ?>">
                        <!-- Icône de déconnexion -->
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 <?php echo $current_page === 'logout.php' ? 'text-indigo-700' : 'text-white'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Se déconnecter</span>
                    </a>
                <?php else: ?>
                    <a href="login.php"
                       class="px-4 py-2 rounded-full font-medium transition-all duration-300 <?php echo $current_page === 'login.php' ? 'bg-white text-indigo-700 shadow-md' : 'text-white hover:bg-indigo-800 hover:shadow-md'; ?>">
                        Se connecter
                    </a>
                    <a href="register.php"
                       class="px-4 py-2 rounded-full font-semibold transition-all duration-300 border-2 border-white text-white hover:bg-white hover:text-indigo-700 hover:shadow-md">
                        S'inscrire
                    </a>
                <?php endif; ?>
            </nav>


            <!-- Bouton hamburger pour mobile -->
            <button id="menu-toggle" class="md:hidden text-white focus:outline-none">
                <svg id="menu-icon" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                </svg>
                <svg id="close-icon" class="w-8 h-8 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>


        <!-- Menu mobile (caché par défaut) -->
        <div id="mobile-menu" class="hidden md:hidden bg-indigo-600 p-4 transform -translate-y-full transition-transform duration-300">
            <nav class="flex flex-col space-y-4">
                <a href="/Projet_Web/collaborative_Library/www/index.php"
                   class="px-5 py-2 rounded-full font-medium transition-all duration-300 <?php echo $current_page === 'index.php' ? 'bg-white text-indigo-700 shadow-md' : 'text-white hover:bg-indigo-800 hover:shadow-md'; ?>">
                    Accueil
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php"
                       class="px-5 py-2 rounded-full font-medium transition-all duration-300 flex items-center space-x-2 <?php echo $current_page === 'profile.php' ? 'bg-white text-indigo-700 shadow-md' : 'text-white hover:bg-indigo-800 hover:shadow-md'; ?>">
                        <!-- Icône utilisateur -->
                        <svg class="w-6 h-6 <?php echo $current_page === 'profile.php' ? 'text-indigo-700' : 'text-white'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </a>
                    <a href="logout.php"
                       class="px-5 py-2 rounded-full font-medium transition-all duration-300 flex items-center space-x-2 <?php echo $current_page === 'logout.php' ? 'bg-white text-indigo-700 shadow-md' : 'text-white hover:bg-indigo-800 hover:shadow-md'; ?>">
                        <!-- Icône de déconnexion -->
                        <svg class="w-6 h-6 <?php echo $current_page === 'logout.php' ? 'text-indigo-700' : 'text-white'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Se déconnecter</span>
                    </a>
                <?php else: ?>
                    <a href="login.php"
                       class="px-5 py-2 rounded-full font-medium transition-all duration-300 <?php echo $current_page === 'login.php' ? 'bg-white text-indigo-700 shadow-md' : 'text-white hover:bg-indigo-800 hover:shadow-md'; ?>">
                        Se connecter
                    </a>
                    <a href="register.php"
                       class="px-5 py-2 rounded-full font-semibold transition-all duration-300 border-2 border-white text-white hover:bg-white hover:text-indigo-700 hover:shadow-md">
                        S'inscrire
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="max-w-7xl mx-auto mt-6">
