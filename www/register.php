<?php
// Démarrer la session
session_start();

// Inclure la connexion à la base de données
require_once 'db_connect.php';

// Initialiser les variables pour les messages d'erreur et de succès
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $username = trim($_POST['username'] ?? '');
    var_dump($username); // Ajoutez ceci juste après la définition de $username
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation des champs
    if (empty($username)) {
        $errors[] = "Le nom d'utilisateur est requis.";
    } elseif (strlen($username) < 3) {
        $errors[] = "Le nom d'utilisateur doit contenir au moins 3 caractères.";
    }

    if (empty($email)) {
        $errors[] = "L'email est requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide.";
    }

    if (empty($password)) {
        $errors[] = "Le mot de passe est requis.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    // Si aucune erreur, procéder à l'inscription
    if (empty($errors)) {
        try {
            // Vérifier si l'utilisateur ou l'email existe déjà
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $errors[] = "Le nom d'utilisateur ou l'email est déjà utilisé.";
            } else {
                // Hacher le mot de passe
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insérer l'utilisateur dans la base de données
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $hashed_password]);

                // Rediriger vers la page de connexion avec un message de succès
                $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                header("Location: login.php?success=" . urlencode($success));
                exit();
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Collaborative Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Inclure le header -->
    <?php require_once '../header/header.php'; ?>

    <main class="max-w-6xl mx-auto mt-8 p-6">
        <div class="max-w-md mx-auto bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-indigo-700 text-center mb-6">S'inscrire à Collaborative Library</h2>

            <!-- Afficher les messages d'erreur -->
            <?php if (!empty($errors)): ?>
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Formulaire d'inscription -->
            <form action="register.php" method="POST" class="space-y-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Nom d'utilisateur</label>
                    <input 
                        type="text" 
                        name="username" 
                        id="username" 
                        value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" 
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                        required
                    >
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" 
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                        required
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                        required
                    >
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                    <input 
                        type="password" 
                        name="confirm_password" 
                        id="confirm_password" 
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                        required
                    >
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors font-semibold"
                >
                    S'inscrire
                </button>
            </form>

            <p class="mt-4 text-center text-gray-600">
                Déjà un compte ? <a href="login.php" class="text-indigo-600 hover:underline">Se connecter</a>
            </p>
        </div>
    </main>

    <!-- Inclure le footer -->
    <?php include '../footer/footer.php'; ?>
</body>
</html>