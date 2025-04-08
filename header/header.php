<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collaborative Library</title>
   
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <header class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">
                <a href="index.php">Collaborative Library</a>
            </h1>
                
            <nav>
                <ul class="flex space-x-4">
                    <li><a href="index.php" class="hover:underline">Accueil</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="profile.php" class="hover:underline"><?php echo htmlspecialchars($_SESSION['username']); ?></a></li>
                        <li><a href="logout.php" class="hover:underline">Se déconnecter</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="hover:underline">Se connecter</a></li>
                        <li><a href="..\register.php" class="hover:underline">S'inscrire</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container mx-auto mt-6">