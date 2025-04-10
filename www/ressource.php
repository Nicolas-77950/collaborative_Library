<?php
session_start();
require_once 'db_connect.php';
require_once '../header/header.php';

// Vérifier si l'ID de la ressource est fourni
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$resourceId = (int)$_GET['id'];

// Récupérer la ressource
$sql = "
    SELECT 
        r.ressource_id,
        r.title,
        r.description,
        r.content,
        r.type,
        r.created_at,
        r.video_url,
        u.username
    FROM 
        ressources r
    INNER JOIN 
        users u ON r.user_id = u.user_id
    WHERE 
        r.ressource_id = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$resourceId]);
$resource = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$resource) {
    header("Location: index.php");
    exit();
}

// Récupérer les fichiers associés
$stmt = $pdo->prepare("SELECT * FROM ressource_files WHERE ressource_id = ?");
$stmt->execute([$resourceId]);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="max-w-6xl mx-auto mt-8 p-6">
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h1 class="text-3xl font-bold text-indigo-700 mb-4"><?php echo htmlspecialchars($resource['title']); ?></h1>
        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($resource['description'] ?? 'Aucune description'); ?></p>
        <div class="text-gray-500 text-sm mb-4">
            <p><span class="font-semibold text-gray-700">Type :</span> <?php echo htmlspecialchars($resource['type']); ?></p>
            <p><span class="font-semibold text-gray-700">Auteur :</span> <?php echo htmlspecialchars($resource['username']); ?></p>
            <p><span class="font-semibold text-gray-700">Date :</span> <?php echo htmlspecialchars($resource['created_at']); ?></p>
        </div>
        <div class="prose max-w-none mb-6">
            <?php echo nl2br(htmlspecialchars($resource['content'])); ?>
        </div>

        <?php if (!empty($resource['video_url'])): ?>
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-3">Vidéo associée</h2>
                <?php
                if (preg_match('/youtube\.com\/watch\?v=([^\&]+)/i', $resource['video_url'], $match)) {
                    $videoId = $match[1];
                    echo '<iframe class="w-full max-w-2xl h-64" src="https://www.youtube.com/embed/' . htmlspecialchars($videoId) . '" frameborder="0" allowfullscreen></iframe>';
                } else {
                    echo '<a href="' . htmlspecialchars($resource['video_url']) . '" target="_blank" class="text-indigo-600 hover:underline">Voir la vidéo</a>';
                }
                ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($files)): ?>
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-3">Fichiers joints</h2>
                <div class="space-y-3">
                    <?php foreach ($files as $file): ?>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                            <a 
                                href="<?php echo htmlspecialchars($file['file_path']); ?>" 
                                target="_blank" 
                                class="text-indigo-600 hover:underline"
                            >
                                <?php echo htmlspecialchars(basename($file['file_path'])); ?> (<?php echo htmlspecialchars($file['file_type']); ?>)
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include '../footer/footer.php'; ?>