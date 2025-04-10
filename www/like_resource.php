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
    // Insérer le like (la contrainte UNIQUE empêchera les doublons)
    $stmt = $pdo->prepare("INSERT INTO likes (user_id, ressource_id) VALUES (?, ?)");
    $stmt->execute([$userId, $resourceId]);

    // Récupérer le nouveau nombre de likes
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE ressource_id = ?");
    $stmt->execute([$resourceId]);
    $likeCount = $stmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'like_count' => $likeCount,
        'has_liked' => true
    ]);
} catch (PDOException $e) {
    // Si l'erreur est due à une violation de la contrainte UNIQUE, cela signifie que l'utilisateur a déjà liké
    if ($e->getCode() == 23000) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Vous avez déjà liké cette ressource.']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
    }
}
exit();