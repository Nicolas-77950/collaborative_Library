<?php
session_start();
// Inclure la connexion à la base de données et le header
require_once 'db_connect.php';
require_once '../header/header.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Récupérer les tags existants pour le champ de sélection
$stmt = $pdo->query("SELECT * FROM tags");
$tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Gestion de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $type = $_POST['type'] ?? '';
    $selectedTags = $_POST['tags'] ?? [];

    // Validation des champs
    $errors = [];
    if (empty($title)) {
        $errors[] = "Le titre est requis.";
    }
    if (empty($content)) {
        $errors[] = "Le contenu est requis.";
    }
    if (!in_array($type, ['tutoriel', 'snippet', 'article', 'autres'])) {
        $errors[] = "Le type est invalide.";
    }

    // Gestion des fichiers téléversés
    $uploadedFiles = [];
    if (!empty($_FILES['files']['name'][0])) {
        $allowedTypes = ['application/pdf', 'video/mp4', 'video/webm', 'image/jpeg', 'image/png'];
        $maxFileSize = 10 * 1024 * 1024; // 10 MB

        foreach ($_FILES['files']['name'] as $key => $fileName) {
            if ($_FILES['files']['error'][$key] === UPLOAD_ERR_OK) {
                $fileType = $_FILES['files']['type'][$key];
                $fileSize = $_FILES['files']['size'][$key];
                $fileTmp = $_FILES['files']['tmp_name'][$key];

                // Validation du fichier
                if (!in_array($fileType, $allowedTypes)) {
                    $errors[] = "Type de fichier non autorisé pour $fileName. Types autorisés : PDF, MP4, WebM, JPEG, PNG.";
                    continue;
                }
                if ($fileSize > $maxFileSize) {
                    $errors[] = "Le fichier $fileName dépasse la taille maximale de 10 MB.";
                    continue;
                }

                // Déplacer le fichier vers le répertoire uploads/
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                $newFileName = uniqid() . '.' . $fileExtension;
                $uploadPath = 'uploads/' . $newFileName;

                if (move_uploaded_file($fileTmp, $uploadPath)) {
                    $uploadedFiles[] = [
                        'path' => $uploadPath,
                        'type' => $fileType
                    ];
                } else {
                    $errors[] = "Erreur lors du téléversement du fichier $fileName.";
                }
            }
        }
    }

    if (empty($errors)) {
        try {
            // Insérer la ressource
            $stmt = $pdo->prepare("INSERT INTO ressources (user_id, title, description, content, type) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $title, $description, $content, $type]);

            // Récupérer l'ID de la ressource insérée
            $resourceId = $pdo->lastInsertId();

            // Insérer les tags associés
            if (!empty($selectedTags)) {
                $stmt = $pdo->prepare("INSERT INTO ressources_tags (ressource_id, tag_id) VALUES (?, ?)");
                foreach ($selectedTags as $tagId) {
                    $stmt->execute([$resourceId, $tagId]);
                }
            }

            // Insérer les fichiers associés
            if (!empty($uploadedFiles)) {
                $stmt = $pdo->prepare("INSERT INTO ressource_files (ressource_id, file_path, file_type) VALUES (?, ?, ?)");
                foreach ($uploadedFiles as $file) {
                    $stmt->execute([$resourceId, $file['path'], $file['type']]);
                }
            }

            // Rediriger vers la page d'index avec un message de succès
            header("Location: index.php?success=Ressource ajoutée avec succès !");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'ajout de la ressource : " . $e->getMessage();
        }
    }
}
?>

<main class="max-w-6xl mx-auto mt-8 p-6">
    <h1 class="text-3xl font-bold text-indigo-700 mb-6">Ajouter une nouvelle ressource</h1>

    <!-- Afficher les erreurs ou le message de succès -->
    <?php if (!empty($errors)): ?>
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="add_resource.php" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-xl shadow-lg">
        <div class="mb-4">
            <label for="title" class="block text-gray-700 font-semibold mb-2">Titre *</label>
            <input 
                type="text" 
                id="title" 
                name="title" 
                value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" 
                class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                required
            >
        </div>

        <div class="mb-4">
            <label for="description" class="block text-gray-700 font-semibold mb-2">Description</label>
            <textarea 
                id="description" 
                name="description" 
                class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                rows="3"
            ><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
        </div>

        <div class="mb-4">
            <label for="content" class="block text-gray-700 font-semibold mb-2">Contenu *</label>
            <textarea 
                id="content" 
                name="content" 
                class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                rows="6"
                required
            ><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
        </div>

        <div class="mb-4">
            <label for="type" class="block text-gray-700 font-semibold mb-2">Type *</label>
            <select 
                id="type" 
                name="type" 
                class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                required
            >
                <option value="tutoriel" <?php echo (isset($_POST['type']) && $_POST['type'] === 'tutoriel') ? 'selected' : ''; ?>>Tutoriel</option>
                <option value="snippet" <?php echo (isset($_POST['type']) && $_POST['type'] === 'snippet') ? 'selected' : ''; ?>>Snippet</option>
                <option value="article" <?php echo (isset($_POST['type']) && $_POST['type'] === 'article') ? 'selected' : ''; ?>>Article</option>
                <option value="autres" <?php echo (isset($_POST['type']) && $_POST['type'] === 'autres') ? 'selected' : ''; ?>>Autres</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="tags" class="block text-gray-700 font-semibold mb-2">Tags (optionnel)</label>
            <select 
                id="tags" 
                name="tags[]" 
                multiple 
                class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
                <?php foreach ($tags as $tag): ?>
                    <option 
                        value="<?php echo $tag['tag_id']; ?>" 
                        <?php echo (isset($_POST['tags']) && in_array($tag['tag_id'], $_POST['tags'])) ? 'selected' : ''; ?>
                    >
                        <?php echo htmlspecialchars($tag['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        
        </div>

        <!-- Champ pour téléverser des fichiers -->
        <div class="mb-4">
            <label for="files" class="block text-gray-700 font-semibold mb-2">Joindre des fichiers (PDF, vidéos, images)</label>
            <div class="relative">
                <input 
                    type="file" 
                    id="files" 
                    name="files[]" 
                    multiple 
                    class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 opacity-0 absolute z-10"
                >
                <div class="w-full p-3 border rounded-lg bg-gray-50 flex items-center cursor-pointer">
                    <svg class="w-6 h-6 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                    <span class="text-gray-500">Choisir des fichiers</span>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-1">Formats autorisés : PDF, MP4, WebM, JPEG, PNG. Taille max : 10 MB.</p>
        </div>

        <div class="flex justify-end">
            <button 
                type="submit" 
                class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition-colors duration-300"
            >
                Ajouter la ressource
            </button>
        </div>
    </form>
</main>

<!-- Inclure le footer -->
<?php include '../footer/footer.php'; ?>