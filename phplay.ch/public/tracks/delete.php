<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

$trackId = $_GET['id'] ?? null;
$playlistId = $_GET['playlist_id'] ?? null;

if ($trackId && $playlistId) {
    // On supprime seulement le LIEN entre la playlist et le morceau
    $stmt = $pdo->prepare("DELETE FROM playlist_tracks WHERE playlist_id = :pid AND track_id = :tid");
    $stmt->execute([':pid' => $playlistId, ':tid' => $trackId]);
}

header("Location: ../playlist/view.php?id=$playlistId");
exit();