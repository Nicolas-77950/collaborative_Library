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

// Supprimer la ressource
try {
    // Vérifier que la ressource appartient à l'utilisateur
    $stmt = $pdo->prepare("SELECT 1 FROM ressources WHERE ressource_id = :id AND user_id = :user_id");
    $stmt->bindParam(':id', $resourceId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    if (!$stmt->fetch()) {
        header('Location: profile.php');
        exit;
    }

    // Supprimer la ressource (les tables liées comme likes, commentaires, favoris, etc., seront supprimées automatiquement grâce aux contraintes ON DELETE CASCADE)
    $stmt = $pdo->prepare("DELETE FROM ressources WHERE ressource_id = :id AND user_id = :user_id");
    $stmt->bindParam(':id', $resourceId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: profile.php?success=Ressource supprimée avec succès.');
    exit;
} catch (PDOException $e) {
    header('Location: profile.php?error=Erreur lors de la suppression de la ressource : ' . urlencode($e->getMessage()));
    exit;
}