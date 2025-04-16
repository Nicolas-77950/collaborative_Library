<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'db_connect.php';

$userId = (int)$_SESSION['user_id'];

// Vérifier si un ID de ressource est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: profile.php');
    exit;
}

$resourceId = (int)$_GET['id'];

// Récupérer la ressource
try {
    $stmt = $pdo->prepare("SELECT * FROM ressources WHERE ressource_id = :id AND user_id = :user_id");
    $stmt->bindParam(':id', $resourceId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $resource = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$resource) {
        header('Location: profile.php');
        exit;
    }
} catch (PDOException $e) {
    die("Erreur lors de la récupération de la ressource : " . $e->getMessage());
}

// Gérer la soumission du formulaire pour modifier la ressource
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $type = $_POST['type'] ?? '';
    $video_url = trim($_POST['video_url'] ?? '');

    // Validation simple
    if (empty($title) || empty($content) || !in_array($type, ['tutoriel', 'snippet', 'article', 'autres'])) {
        $error = "Veuillez remplir tous les champs requis correctement.";
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE ressources 
                SET title = :title, description = :description, content = :content, type = :type, video_url = :video_url, updated_at = CURRENT_TIMESTAMP
                WHERE ressource_id = :id AND user_id = :user_id
            ");
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':video_url', $video_url);
            $stmt->bindParam(':id', $resourceId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            header('Location: profile.php?success=Ressource mise à jour avec succès.');
            exit;
        } catch (PDOException $e) {
            $error = "Erreur lors de la mise à jour de la ressource : " . $e->getMessage();
        }
    }
}

require_once 'header/header.php';
?>

<main class="max-w-6xl mx-auto mt-8 p-6">
    <h1 class="text-2xl font-bold text-indigo-700 mb-6">Modifier la ressource</h1>

    <!-- Afficher un message d'erreur si présent -->
    <?php if (isset($error)): ?>
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-4">
            <label for="title" class="block text-gray-700 font-semibold mb-2">Titre</label>
            <input 
                type="text" 
                name="title" 
                id="title" 
                value="<?php echo htmlspecialchars($resource['title']); ?>" 
                class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                required
            >
        </div>

        <div class="mb-4">
            <label for="description" class="block text-gray-700 font-semibold mb-2">Description</label>
            <textarea 
                name="description" 
                id="description" 
                class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                rows="4"
            ><?php echo htmlspecialchars($resource['description'] ?? ''); ?></textarea>
        </div>

        <div class="mb-4">
            <label for="content" class="block text-gray-700 font-semibold mb-2">Contenu</label>
            <textarea 
                name="content" 
                id="content" 
                class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                rows="6" 
                required
            ><?php echo htmlspecialchars($resource['content']); ?></textarea>
        </div>

        <div class="mb-4">
            <label for="type" class="block text-gray-700 font-semibold mb-2">Type</label>
            <select 
                name="type" 
                id="type" 
                class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                required
            >
                <option value="tutoriel" <?php echo $resource['type'] === 'tutoriel' ? 'selected' : ''; ?>>Tutoriel</option>
                <option value="snippet" <?php echo $resource['type'] === 'snippet' ? 'selected' : ''; ?>>Snippet</option>
                <option value="article" <?php echo $resource['type'] === 'article' ? 'selected' : ''; ?>>Article</option>
                <option value="autres" <?php echo $resource['type'] === 'autres' ? 'selected' : ''; ?>>Autres</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="video_url" class="block text-gray-700 font-semibold mb-2">URL de la vidéo (optionnel)</label>
            <input 
                type="url" 
                name="video_url" 
                id="video_url" 
                value="<?php echo htmlspecialchars($resource['video_url'] ?? ''); ?>" 
                class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
        </div>

        <div class="flex justify-end space-x-3">
            <a href="profile.php" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700 transition-colors">
                Annuler
            </a>
            <button 
                type="submit" 
                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition-colors"
            >
                Enregistrer les modifications
            </button>
        </div>
    </form>
</main>

<?php include 'footer/footer.php'; ?>
</body>
</html>