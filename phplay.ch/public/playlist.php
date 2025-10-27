<?php
const DATABASE_CONFIGURATION_FILE = __DIR__ . '/../src/config/database.ini';

// Lecture du fichier de configuration
$config = parse_ini_file(DATABASE_CONFIGURATION_FILE, true);
if (!$config) {
    throw new Exception("Erreur lors de la lecture du fichier de configuration : " . DATABASE_CONFIGURATION_FILE);
}

$host = $config['host'];
$port = $config['port'];
$database = $config['database'];
$username = $config['username'];
$password = $config['password'];

// Connexion PDO
$pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Sélection de la base
$sql = "USE `$database`;";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Récupération de l’ID de la playlist depuis l’URL
$playlistId = $_GET['id'] ?? null;
if (!$playlistId) {
    die("Aucune playlist spécifiée.");
}

// Récupération des infos de la playlist
$sql = "SELECT p.*, u.first_name, u.last_name 
        FROM playlists p
        JOIN users u ON u.id = p.user_id
        WHERE p.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $playlistId, PDO::PARAM_INT);
$stmt->execute();
$playlist = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$playlist) {
    die("Playlist introuvable.");
}

// Création de la table tracks si besoin
$sql = "CREATE TABLE IF NOT EXISTS tracks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    artist VARCHAR(255) NOT NULL,
    genre VARCHAR(100),
    duration INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Création de la table de liaison si besoin
$sql = "CREATE TABLE IF NOT EXISTS playlist_tracks (
    playlist_id INT NOT NULL,
    track_id INT NOT NULL,
    PRIMARY KEY (playlist_id, track_id),
    FOREIGN KEY (playlist_id) REFERENCES playlists(id) ON DELETE CASCADE,
    FOREIGN KEY (track_id) REFERENCES tracks(id) ON DELETE CASCADE
);";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Récupération des morceaux de la playlist
$sql = "SELECT t.*
        FROM tracks t
        JOIN playlist_tracks pt ON t.id = pt.track_id
        WHERE pt.playlist_id = :id
        ORDER BY t.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $playlistId, PDO::PARAM_INT);
$stmt->execute();
$tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">

    <title><?= htmlspecialchars($playlist['playlist_name']) ?> | PHPlay</title>
</head>
<body>
<main class="container">
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
</main>
</body>
</html>
