<?php
// Inclure la connexion à la base de données et le header
require_once 'db_connect.php';
require_once '../header/header.php';

// Requête SQL pour récupérer les ressources
$sql = "
    SELECT 
        r.ressource_id,
        r.title,
        r.description,
        r.type,
        r.created_at,
        u.username,
        COUNT(l.like_id) AS like_count
    FROM 
        ressources r
    INNER JOIN 
        users u ON r.user_id = u.user_id
    LEFT JOIN 
        likes l ON r.ressource_id = l.ressource_id
    GROUP BY 
        r.ressource_id, r.title, r.description, r.type, r.created_at, u.username
    ORDER BY 
        r.created_at DESC
";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $ressources = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des ressources : " . $e->getMessage());
}
?>

    
    <main class="max-w-4xl mx-auto mt-6 p-4">
        <?php if (empty($ressources)): ?>
            <p class="text-center text-gray-600">Aucune ressource disponible pour le moment.</p>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($ressources as $ressource): ?>
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                        <h2 class="text-xl font-semibold text-blue-600">
                            <?php echo htmlspecialchars($ressource['title']); ?>
                        </h2>
                        <p class="text-gray-600 mt-2">
                            <?php echo htmlspecialchars($ressource['description'] ?? 'Aucune description'); ?>
                        </p>
                        <div class="mt-4 text-sm text-gray-500">
                            <p><span class="font-medium">Type :</span> <?php echo htmlspecialchars($ressource['type']); ?></p>
                            <p><span class="font-medium">Auteur :</span> <?php echo htmlspecialchars($ressource['username']); ?></p>
                            <p><span class="font-medium">Date :</span> <?php echo htmlspecialchars($ressource['created_at']); ?></p>
                            <p><span class="font-medium">Likes :</span> <?php echo $ressource['like_count']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Inclure le footer -->
    <?php include '../footer/footer.php'; ?>
</body>
</html>