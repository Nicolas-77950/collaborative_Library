<?php
session_start();
require_once 'db_connect.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Veuillez vous connecter pour liker une ressource.']);
    exit();
}

$userId = (int)$_SESSION['user_id'];
$resourceId = isset($_POST['resource_id']) ? (int)$_POST['resource_id'] : 0;

// Vérifier si l'ID de la ressource est valide
if ($resourceId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de ressource invalide.']);
    exit();
}

try {
    // Vérifier si l'utilisateur a déjà liké la ressource
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE user_id = ? AND ressource_id = ?");
    $stmt->execute([$userId, $resourceId]);
    $hasLiked = $stmt->fetchColumn() > 0;

    if ($hasLiked) {
        // L'utilisateur a déjà liké, donc on supprime le like
        $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND ressource_id = ?");
        $stmt->execute([$userId, $resourceId]);
        $action = 'unliked';
    } else {
        // L'utilisateur n'a pas encore liké, donc on ajoute le like
        $stmt = $pdo->prepare("INSERT INTO likes (user_id, ressource_id) VALUES (?, ?)");
        $stmt->execute([$userId, $resourceId]);
        $action = 'liked';
    }

    // Récupérer le nouveau nombre de likes
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE ressource_id = ?");
    $stmt->execute([$resourceId]);
    $likeCount = $stmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'action' => $action, // Indique si on a liké ou déliké
        'like_count' => $likeCount,
        'has_liked' => $action === 'liked' // true si liké, false si déliké
    ]);
} catch (PDOException $e) {
    // Si l'erreur est due à une violation de la contrainte UNIQUE, cela ne devrait pas arriver ici
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
}
exit();