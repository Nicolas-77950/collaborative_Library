<?php
session_start();
require_once 'db_connect.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Veuillez vous connecter pour commenter.']);
    exit();
}

$userId = (int)$_SESSION['user_id'];
$resourceId = isset($_POST['resource_id']) ? (int)$_POST['resource_id'] : 0;
$commentContent = isset($_POST['content']) ? trim($_POST['content']) : '';

// Vérifier les données
if ($resourceId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de ressource invalide.']);
    exit();
}

if (empty($commentContent)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Le commentaire ne peut pas être vide.']);
    exit();
}

try {
    // Insérer le commentaire dans la table commentaires
    $stmt = $pdo->prepare("INSERT INTO commentaires (user_id, ressource_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $resourceId, $commentContent]);

    // Récupérer le nombre total de commentaires pour cette ressource
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM commentaires WHERE ressource_id = ?");
    $stmt->execute([$resourceId]);
    $commentCount = $stmt->fetchColumn();

    // Récupérer le nom d'utilisateur de l'auteur du commentaire
    $stmt = $pdo->prepare("SELECT username FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $username = $stmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'comment_count' => $commentCount,
        'comment' => [
            'author' => $username,
            'content' => $commentContent
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
}
exit();