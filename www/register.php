<?php
// Démarrer la session
session_start();

// Inclure la connexion à la base de données
require_once 'db_connect.php';

// Initialiser les variables pour les messages d'erreur et de succès
$errors = [];
$success = '';
$form_username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $form_username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation des champs
    if (empty($form_username)) {
        $errors[] = "Le nom d'utilisateur est requis.";
    } elseif (strlen($form_username) < 3) {
        $errors[] = "Le nom d'utilisateur doit contenir au moins 3 caractères.";
    }

    if (empty($email)) {
        $errors[] = "L'email est requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide.";
    }

    // Validation du mot de passe avec les critères
    if (empty($password)) {
        $errors[] = "Le mot de passe est requis.";
    } else {
        $length_ok = strlen($password) >= 8;
        $uppercase_ok = preg_match('/[A-Z]/', $password);
        $special_char_ok = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password);

        if (!$length_ok) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
        }
        if (!$uppercase_ok) {
            $errors[] = "Le mot de passe doit contenir au moins une majuscule.";
        }
        if (!$special_char_ok) {
            $errors[] = "Le mot de passe doit contenir au moins un caractère spécial.";
        }
    }

    if ($password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    // Si aucune erreur, procéder à l'inscription
    if (empty($errors)) {
        try {
            // Vérifier si l'utilisateur ou l'email existe déjà
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$form_username, $email]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $errors[] = "Le nom d'utilisateur ou l'email est déjà utilisé.";
            } else {
                // Hacher le mot de passe
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insérer l'utilisateur dans la base de données
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$form_username, $email, $hashed_password]);

                // Stocker le message de succès dans la session
                $_SESSION['success_message'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";

                // Rediriger vers la page de connexion sans message dans l'URL
                header("Location: login.php");
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
            <form action="register.php" method="POST" class="space-y-6" autocomplete="off">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Nom d'utilisateur</label>
                    <input 
                        type="text" 
                        name="username" 
                        id="username" 
                        value="<?php echo isset($form_username) ? htmlspecialchars($form_username) : ''; ?>" 
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                        required
                        autocomplete="off"
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
                        autocomplete="off"
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
                        autocomplete="off"
                        onkeyup="checkPasswordStrength()"
                    >
                    <!-- Barre de progression -->
                    <div class="mt-2">
                        <div id="password-strength" class="h-2 rounded-full bg-gray-200 transition-all duration-300"></div>
                        <p id="password-strength-text" class="text-sm text-gray-600 mt-1"></p>
                    </div>
                    <!-- Messages d'erreur pour les critères -->
                    <ul id="password-criteria" class="mt-2 text-sm space-y-1">
                        <li id="length-criteria" class="text-red-500">✘ Veuillez utiliser au moins 8 caractères</li>
                        <li id="uppercase-criteria" class="text-red-500">✘ Veuillez inclure au moins 1 majuscule</li>
                        <li id="special-char-criteria" class="text-red-500">✘ Veuillez inclure au moins 1 caractère spécial</li>
                    </ul>
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                    <input 
                        type="password" 
                        name="confirm_password" 
                        id="confirm_password" 
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                        required
                        autocomplete="off"
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

    <!-- Inclure le fichier JavaScript -->
    <script src="/Projet_Web/collaborative_Library/js/password.js"></script>

    <!-- Inclure le footer -->
    <?php include '../footer/footer.php'; ?>
</body>
</html>