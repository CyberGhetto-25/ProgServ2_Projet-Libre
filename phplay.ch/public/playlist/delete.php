<?php
require_once __DIR__ . '/../includes/config.php';

$playlistId = $_GET['id'] ?? null;

if ($playlistId) {
    // 1. Supprimer d'abord les liens dans playlist_tracks pour éviter les erreurs de clé étrangère
    $stmt = $pdo->prepare("DELETE FROM playlist_tracks WHERE playlist_id = :id");
    $stmt->execute([':id' => $playlistId]);

    // 2. Supprimer la playlist
    $stmt = $pdo->prepare("DELETE FROM playlists WHERE id = :id");
    $stmt->execute([':id' => $playlistId]);
}

header("Location: ../index.php");
exit();