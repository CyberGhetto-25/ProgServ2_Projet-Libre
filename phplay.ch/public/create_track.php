<?php
const DATABASE_CONFIGURATION_FILE = __DIR__ . '/../src/config/database.ini';

// Lecture du fichier de configuration
$config = parse_ini_file(DATABASE_CONFIGURATION_FILE, true);
if (!$config) {
    throw new Exception("Erreur lors de la lecture du fichier : " . DATABASE_CONFIGURATION_FILE);
}

$host = $config['host'];
$port = $config['port'];
$database = $config['database'];
$username = $config['username'];
$password = $config['password'];


// Connexion à la base

$pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Sélection de la base
$sql = "USE `$database`;";
$stmt = $pdo->prepare($sql);
$stmt->execute();


// Création des tables 

$pdo->prepare("CREATE TABLE IF NOT EXISTS tracks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    artist VARCHAR(255) NOT NULL,
    genre VARCHAR(100),
    duration INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);")->execute();

$pdo->prepare("CREATE TABLE IF NOT EXISTS playlist_tracks (
    playlist_id INT NOT NULL,
    track_id INT NOT NULL,
    PRIMARY KEY (playlist_id, track_id),
    FOREIGN KEY (playlist_id) REFERENCES playlists(id) ON DELETE CASCADE,
    FOREIGN KEY (track_id) REFERENCES tracks(id) ON DELETE CASCADE
);")->execute();

// Récupération de la playlist cible

$playlistId = $_GET['playlist_id'] ?? null;
if (!$playlistId) {
    die("Aucune playlist spécifiée.");
}

$sql = "SELECT * FROM playlists WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $playlistId, PDO::PARAM_INT);
$stmt->execute();
$playlist = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$playlist) {
    die("Playlist introuvable.");
}


// Gestion du formulaire

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title    = trim($_POST["title"] ?? '');
    $artist   = trim($_POST["artist"] ?? '');
    $genre    = trim($_POST["genre"] ?? '');
    $duration = trim($_POST["duration"] ?? '');

    $errors = [];

    if (empty($title) || strlen($title) < 2) {
        $errors[] = "Le titre doit contenir au moins 2 caractères.";
    }
    if (empty($artist) || strlen($artist) < 2) {
        $errors[] = "L'artiste doit contenir au moins 2 caractères.";
    }
    if ($duration !== '' && (!ctype_digit($duration) || $duration < 0)) {
        $errors[] = "La durée doit être un nombre positif (en secondes).";
    }

    if (empty($errors)) {
        // Insertion du morceau dans la table tracks
        $sql = "INSERT INTO tracks (title, artist, genre, duration)
                VALUES (:title, :artist, :genre, :duration)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':artist', $artist);
        $stmt->bindValue(':genre', $genre !== '' ? $genre : null);
        $stmt->bindValue(':duration', $duration !== '' ? $duration : null);
        $stmt->execute();

        $trackId = $pdo->lastInsertId();

        // Association du morceau à la playlist
        $sql = "INSERT INTO playlist_tracks (playlist_id, track_id)
                VALUES (:playlist_id, :track_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':playlist_id', $playlistId);
        $stmt->bindValue(':track_id', $trackId);
        $stmt->execute();

        // Redirection vers la page de la playlist
        header("Location: playlist.php?id=" . $playlistId);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">

    <title>Ajouter un morceau | PHPlay</title>
</head>
<body>
<main class="container">
    <h1>Ajouter un morceau à la playlist</h1>
    <h3><?= htmlspecialchars($playlist['playlist_name']) ?></h3>

    <?php if ($_SERVER["REQUEST_METHOD"] === "POST"): ?>
        <?php if (empty($errors)): ?>
            <p style="color: green;">Le morceau a été ajouté avec succès</p>
        <?php else: ?>
            <p style="color: red;">Le formulaire contient des erreurs :</p>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>

    <form action="create_track.php?playlist_id=<?= $playlistId ?>" method="POST">
        <label for="title">Titre du morceau *</label>
        <input type="text" id="title" name="title" required minlength="2"
               value="<?= htmlspecialchars($title ?? '') ?>">

        <label for="artist">Artiste *</label>
        <input type="text" id="artist" name="artist" required minlength="2"
               value="<?= htmlspecialchars($artist ?? '') ?>">

        <label for="genre">Genre</label>
        <input type="text" id="genre" name="genre"
               value="<?= htmlspecialchars($genre ?? '') ?>">

        <label for="duration">Durée (secondes)</label>
        <input type="number" id="duration" name="duration" min="0"
               value="<?= htmlspecialchars($duration ?? '') ?>">

        <button type="submit">Ajouter à la playlist</button>
    </form>

    <p>
        <a href="playlist.php?id=<?= $playlistId ?>"><button>Retour à la playlist</button></a>
    </p>
</main>
</body>
</html>
