<?php
$pageTitle = "Voir une playlist | PHPlay";
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/header.php';

// Récupération de l’ID de la playlist
$playlistId = $_GET['id'] ?? null;
if (!$playlistId) {
    die("Aucune playlist spécifiée.");
}

// Récupération des infos de la playlist
$sql = "SELECT p.*, u.first_name, u.last_name FROM playlists p JOIN users u ON u.id = p.user_id WHERE p.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $playlistId, PDO::PARAM_INT);
$stmt->execute();
$playlist = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$playlist) {
    die("Playlist introuvable.");
}

// Récupération des morceaux de la playlist
$sql = "SELECT t.* FROM tracks t JOIN playlist_tracks pt ON t.id = pt.track_id WHERE pt.playlist_id = :id ORDER BY t.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $playlistId, PDO::PARAM_INT);
$stmt->execute();
$tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1><?= htmlspecialchars($playlist['playlist_name']) ?></h1>
<p>
    Créée par : <?= htmlspecialchars($playlist['first_name'] . ' ' . $playlist['last_name']) ?><br>
    Visibilité : <?= $playlist['is_public'] ? 'Publique' : 'Privée' ?>
</p>

<p>
    <a href="../index.php"><button>⬅ Retour à l’accueil</button></a>
    <a href="create_track.php?playlist_id=<?= $playlistId ?>"><button>➕ Ajouter un morceau</button></a>
</p>

<h2>🎵 Morceaux de la playlist</h2>
<?php if (count($tracks) === 0): ?>
    <p>Aucun morceau dans cette playlist pour le moment.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Artiste</th>
                <th>Genre</th>
                <th>Durée (s)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tracks as $track): ?>
                <tr>
                    <td><?= htmlspecialchars($track['title']) ?></td>
                    <td><?= htmlspecialchars($track['artist']) ?></td>
                    <td><?= htmlspecialchars($track['genre'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($track['duration'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>