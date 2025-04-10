<?php
<<<<<<< HEAD
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
=======
require_once 'db_connect.php'; 

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Nettoyage et validation des entrées
        $username = trim($_POST['username'] ?? '');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';

        // Vérification des champs
        if (empty($username) || !$email || empty($password)) {
            $message = "Tous les champs sont requis et l'email doit être valide.";
        } else {
            // Vérification de l'unicité de l'email
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $message = "Cet email est déjà utilisé.";
            } else {
                // Vérification de l'unicité du nom d'utilisateur
                $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = :username");
                $stmt->bindParam(':username', $username);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $message = "Ce nom d'utilisateur est déjà pris.";
                } else {
                    // Hachage du mot de passe
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);

                    // Insertion dans la base de données
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) 
                                          VALUES (:username, :email, :password)");
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':password', $password_hash);

                    if ($stmt->execute()) {
                        $message = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                    } else {
                        $message = "Erreur lors de l'inscription.";
                    }
                }
            }
        }
    } catch (PDOException $e) {
        $message = "Erreur : " . $e->getMessage();
>>>>>>> 5126d5dc3ea05d6bb1e974a698656258599cddc2
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
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
=======
    <title>Inscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Inscription</h2>
        
        <?php if (!empty($message)): ?>
            <div class="mb-4 p-3 rounded <?php echo strpos($message, 'réussie') !== false ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Nom d'utilisateur</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>"
                    required 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                    required 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
            </div>

            <button 
                type="submit" 
                class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                S'inscrire
            </button>
        </form>
    </div>
>>>>>>> 5126d5dc3ea05d6bb1e974a698656258599cddc2
</body>
</html>