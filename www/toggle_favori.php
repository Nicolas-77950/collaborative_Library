<?php
session_start();
require_once 'db_connect.php'; 

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Vous devez être connecté pour gérer les favoris.';
    echo json_encode($response);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$resourceId = isset($_POST['resource_id']) ? (int)$_POST['resource_id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($resourceId <= 0 || !in_array($action, ['add', 'remove'])) {
    $response['message'] = 'Requête invalide.';
    echo json_encode($response);
    exit;
}

try {
    // Vérifier si la ressource existe
    $stmt = $pdo->prepare("SELECT 1 FROM ressources WHERE ressource_id = ?");
    $stmt->execute([$resourceId]);
    if (!$stmt->fetch()) {
        $response['message'] = 'Ressource introuvable.';
        echo json_encode($response);
        exit;
    }

    if ($action === 'add') {
        // Ajouter aux favoris
        $stmt = $pdo->prepare("INSERT INTO favoris (user_id, ressource_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE created_at = CURRENT_TIMESTAMP");
        $stmt->execute([$userId, $resourceId]);
        $isFavorited = true;
    } else {
        // Supprimer des favoris
        $stmt = $pdo->prepare("DELETE FROM favoris WHERE user_id = ? AND ressource_id = ?");
        $stmt->execute([$userId, $resourceId]);
        $isFavorited = false;
    }

    // Compter le nombre total de favoris pour cette ressource
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM favoris WHERE ressource_id = ?");
    $stmt->execute([$resourceId]);
    $favoriCount = $stmt->fetchColumn();

    $response['success'] = true;
    $response['is_favorited'] = $isFavorited;
    $response['favori_count'] = $favoriCount;
} catch (PDOException $e) {
    $response['message'] = 'Erreur lors de la gestion des favoris : ' . $e->getMessage();
    error_log('Erreur PDO dans toggle_favori.php: ' . $e->getMessage());
}

echo json_encode($response);