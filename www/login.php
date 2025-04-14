<?php
// Démarrer la session
session_start();

// Inclure la connexion à la base de données
require_once 'db_connect.php';

// Initialiser les variables pour les messages d'erreur
$errors = [];

// Récupérer le message de succès de la session, s'il existe
$success_message = '';
if (isset($_SESSION['success_message']) && !empty($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    // Supprimer le message de la session après l'avoir récupéré
    unset($_SESSION['success_message']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation des champs
    if (empty($email)) {
        $errors[] = "L'email est requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide.";
    }

    if (empty($password)) {
        $errors[] = "Le mot de passe est requis.";
    }

    // Si aucune erreur, vérifier les identifiants
    if (empty($errors)) {
        try {
            // Rechercher l'utilisateur par email
            $stmt = $pdo->prepare("SELECT user_id, username, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Connexion réussie : stocker les informations de l'utilisateur dans la session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $email; 

                // Rediriger vers la page d'accueil
                header("Location: index.php");
                exit();
            } else {
                $errors[] = "Email ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de la connexion : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Collaborative Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Inclure le header -->
    <?php require_once '../header/header.php'; ?>

    <main class="max-w-6xl mx-auto mt-8 p-6">
        <div class="max-w-md mx-auto bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-indigo-700 text-center mb-6">Se connecter à Collaborative Library</h2>

            <!-- Afficher le message de succès -->
            <?php if (!empty($success_message)): ?>
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg text-center">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

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

            <!-- Formulaire de connexion -->
            <form action="login.php" method="POST" class="space-y-6" autocomplete="off">
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
                    >
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors font-semibold"
                >
                    Se connecter
                </button>
            </form>

            <p class="mt-4 text-center text-gray-600">
                Pas de compte ? <a href="register.php" class="text-indigo-600 hover:underline">S'inscrire</a>
            </p>
        </div>
    </main>

    <!-- Inclure le footer -->
    <?php include '../footer/footer.php'; ?>
</body>
</html>