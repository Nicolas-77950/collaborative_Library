<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'db_connect.php';

// Récupérer les informations de l'utilisateur connecté
$userId = (int)$_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'Utilisateur';
$email = $_SESSION['email'] ?? 'Email non disponible';

// Récupérer les ressources publiées par l'utilisateur
try {
    $stmt = $pdo->prepare("
        SELECT 
            r.ressource_id,
            r.title,
            r.description,
            r.type,
            r.created_at,
            r.video_url
        FROM 
            ressources r
        WHERE 
            r.user_id = :user_id
        ORDER BY 
            r.created_at DESC
    ");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $ressources = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des ressources : " . $e->getMessage());
}

require_once '../header/header.php';
?>

<main class="max-w-6xl mx-auto mt-8 p-6">
    <!-- Message de bienvenue -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6 text-center">
        <h1 class="text-2xl font-bold text-indigo-700">
            Bienvenue, <?php echo htmlspecialchars($email); ?> !
        </h1>
        <p class="text-gray-600 mt-2">Ceci est votre page de profil.</p>
    </div>

    <!-- Afficher un message de succès ou d'erreur si présent -->
    <?php if (isset($_GET['success'])): ?>
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Conteneur pour le message de confirmation de suppression -->
    <div id="delete-confirmation" class="hidden mb-4 p-4 bg-yellow-100 text-yellow-700 rounded-lg flex justify-between items-center">
        <span>Êtes-vous sûr de vouloir supprimer cette ressource ? Cette action est irréversible.</span>
        <div class="space-x-3">
            <button id="confirm-delete-btn" class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                Confirmer
            </button>
            <button id="cancel-delete-btn" class="px-3 py-1 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Annuler
            </button>
        </div>
    </div>

    <!-- Liste des ressources publiées -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Vos ressources publiées</h2>
        <?php if (empty($ressources)): ?>
            <p class="text-center text-gray-500 text-lg">Vous n'avez publié aucune ressource pour le moment.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($ressources as $ressource): ?>
                    <div class="relative bg-white rounded-xl shadow-lg p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 border-l-4 
                        <?php echo $ressource['type'] === 'tutoriel' ? 'border-indigo-500' : 
                                  ($ressource['type'] === 'article' ? 'border-green-500' : 
                                  ($ressource['type'] === 'snippet' ? 'border-purple-500' : 'border-gray-500')); ?>">
                        <!-- Icône selon le type -->
                        <div class="absolute top-4 right-4">
                            <span class="text-2xl">
                                <?php if ($ressource['type'] === 'tutoriel'): ?>
                                    📚
                                <?php elseif ($ressource['type'] === 'article'): ?>
                                    📰
                                <?php elseif ($ressource['type'] === 'snippet'): ?>
                                    💻
                                <?php else: ?>
                                    📦
                                <?php endif; ?>
                            </span>
                        </div>
                        <h3 class="text-xl font-bold text-indigo-700 hover:text-indigo-900 transition-colors">
                            <a href="ressource.php?id=<?php echo $ressource['ressource_id']; ?>">
                                <?php echo htmlspecialchars($ressource['title']); ?>
                            </a>
                        </h3>
                        <p class="text-gray-600 mt-3 line-clamp-3">
                            <?php echo htmlspecialchars($ressource['description'] ?? 'Aucune description'); ?>
                        </p>
                        <div class="mt-4 text-sm text-gray-500 space-y-1">
                            <p><span class="font-semibold text-gray-700">Type :</span> 
                                <span class="capitalize <?php echo $ressource['type'] === 'tutoriel' ? 'text-indigo-600' : 
                                                          ($ressource['type'] === 'article' ? 'text-green-600' : 
                                                          ($ressource['type'] === 'snippet' ? 'text-purple-600' : 'text-gray-600')); ?>">
                                    <?php echo htmlspecialchars($ressource['type']); ?>
                                </span>
                            </p>
                            <p><span class="font-semibold text-gray-700">Date :</span> <?php echo htmlspecialchars($ressource['created_at']); ?></p>
                        </div>

                        <!-- Boutons pour modifier et supprimer -->
                        <div class="mt-4 flex space-x-3">
                            <a href="edit_resource.php?id=<?php echo $ressource['ressource_id']; ?>" 
                               class="inline-flex items-center px-3 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Modifier
                            </a>
                            <button 
                                onclick="showDeleteConfirmation(<?php echo $ressource['ressource_id']; ?>)"
                                class="inline-flex items-center px-3 py-2 bg-red-600 text-white font-semibold rounded-lg shadow-md hover:bg-red-700 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4m-4 0V3a1 1 0 011-1h2a1 1 0 011 1v1m-7 3h10"/>
                                </svg>
                                Supprimer
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Inclure le fichier JavaScript pour la gestion de la suppression -->
<script src="../js/delete_resource.js"></script>

<?php include '../footer/footer.php'; ?>
</body>
</html>