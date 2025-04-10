<?php
session_start();
// Inclure la connexion à la base de données et le header
require_once 'db_connect.php';
require_once '../header/header.php';

// Requête SQL (inchangée)
$sql = "
    SELECT 
        r.ressource_id,
        r.title,
        r.description,
        r.type,
        r.created_at,
        u.username,
        COUNT(l.like_id) AS like_count,
        COUNT(c.commentaire_id) AS comment_count,
        GROUP_CONCAT(c.content SEPARATOR '|||') AS comments_content,
        GROUP_CONCAT(u2.username SEPARATOR '|||') AS comments_authors
    FROM 
        ressources r
    INNER JOIN 
        users u ON r.user_id = u.user_id
    LEFT JOIN 
        likes l ON r.ressource_id = l.ressource_id
    LEFT JOIN 
        commentaires c ON r.ressource_id = c.ressource_id
    LEFT JOIN 
        users u2 ON c.user_id = u2.user_id
    GROUP BY 
        r.ressource_id, r.title, r.description, r.type, r.created_at, u.username
    ORDER BY 
        r.created_at DESC
";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $ressources = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($ressources as &$ressource) {
        if ($ressource['comments_content']) {
            $ressource['comments'] = array_combine(
                explode('|||', $ressource['comments_authors']),
                explode('|||', $ressource['comments_content'])
            );
        } else {
            $ressource['comments'] = [];
        }
        unset($ressource['comments_content'], $ressource['comments_authors']);
    }
} catch (PDOException $e) {
    die("Erreur lors de la récupération des ressources : " . $e->getMessage());
}

// Vérifier si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['user_id']);
?>

<main class="max-w-6xl mx-auto mt-8 p-6">
    <!-- Bouton "Ajouter des ressources" -->
    <div class="mb-6 flex justify-end">
        <button 
            id="add-resource-btn" 
            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition-colors duration-300"
        >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajouter des ressources
        </button>
    </div>

    <!-- Div pour le message temporaire -->
    <div id="login-message" class="hidden mb-4 p-4 bg-yellow-100 text-yellow-700 rounded-lg">
        Veuillez vous connecter pour ajouter une ressource.
    </div>

    <!-- Afficher un message de succès si présent -->
    <?php if (isset($_GET['success'])): ?>
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($ressources)): ?>
        <p class="text-center text-gray-500 text-lg">Aucune ressource disponible pour le moment.</p>
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
                    <h2 class="text-2xl font-bold text-indigo-700 hover:text-indigo-900 transition-colors">
                        <a href="ressource.php?id=<?php echo $ressource['ressource_id']; ?>">
                            <?php echo htmlspecialchars($ressource['title']); ?>
                        </a>
                    </h2>
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
                        <p><span class="font-semibold text-gray-700">Auteur :</span> <?php echo htmlspecialchars($ressource['username']); ?></p>
                        <p><span class="font-semibold text-gray-700">Date :</span> <?php echo htmlspecialchars($ressource['created_at']); ?></p>
                        <div class="flex items-center space-x-4">
                            <p class="flex items-center">
                                <svg class="w-5 h-5 mr-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                <span><?php echo $ressource['like_count']; ?> Likes</span>
                            </p>
                            <!-- Bouton commentaires -->
                            <button 
                                class="flex items-center space-x-2 text-gray-600 hover:text-indigo-600 focus:outline-none transition-colors"
                                onclick="toggleComments('comments-<?php echo $ressource['ressource_id']; ?>')"
                            >
                                <svg class="w-5 h-5 transform transition-transform hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                                <span><?php echo $ressource['comment_count']; ?> Commentaires</span>
                            </button>
                        </div>
                    </div>
                    <!-- Section commentaires -->
                    <div 
                        id="comments-<?php echo $ressource['ressource_id']; ?>" 
                        class="hidden mt-4 p-4 bg-gray-100 rounded-lg"
                    >
                        <?php if (empty($ressource['comments'])): ?>
                            <p class="text-gray-500 italic">Aucun commentaire pour le moment.</p>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach ($ressource['comments'] as $author => $content): ?>
                                    <div class="border-l-4 border-indigo-200 pl-3">
                                        <p class="font-semibold text-gray-700"><?php echo htmlspecialchars($author); ?></p>
                                        <p class="text-gray-600"><?php echo htmlspecialchars($content); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<!-- Lien vers le fichier JavaScript externe -->
<script src="../js/commentaire.js"></script>

<!-- Définir la variable globale pour add_ressource.js -->
<script>
    const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
</script>
<script src="../js/add_ressource.js"></script>

<script src="../js/add_ressource.js"></script>

<!-- Inclure le footer -->
<?php include '../footer/footer.php'; ?>
</body>
</html>