<?php
$pageTitle = "Ajouter un morceau | PHPlay";
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/header.php';

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
$title = '';
$artist = '';
$genre = '';
$duration = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? '');
    $artist = trim($_POST["artist"] ?? '');
    $genre = trim($_POST["genre"] ?? '');
    $duration = trim($_POST["duration"] ?? '');

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
        // Insertion du morceau
        $sql = "INSERT INTO tracks (title, artist, genre, duration) VALUES (:title, :artist, :genre, :duration)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':artist', $artist);
        $stmt->bindValue(':genre', $genre !== '' ? $genre : null);
        $stmt->bindValue(':duration', $duration !== '' ? $duration : null);
        $stmt->execute();
        $trackId = $pdo->lastInsertId();

        // Association du morceau à la playlist
        $sql = "INSERT INTO playlist_tracks (playlist_id, track_id) VALUES (:playlist_id, :track_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':playlist_id', $playlistId);
        $stmt->bindValue(':track_id', $trackId);
        $stmt->execute();

        header("Location: ../playlists/view.php?id=" . $playlistId);
        exit();
    }
}
?>

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

<form action="create.php?playlist_id=<?= $playlistId ?>" method="POST">
    <label for="title">Titre du morceau *</label>
    <input type="text" id="title" name="title" required minlength="2" value="<?= htmlspecialchars($title) ?>">

    <label for="artist">Artiste *</label>
    <input type="text" id="artist" name="artist" required minlength="2" value="<?= htmlspecialchars($artist) ?>">

    <label for="genre">Genre</label>
    <input type="text" id="genre" name="genre" value="<?= htmlspecialchars($genre) ?>">

    <label for="duration">Durée (secondes)</label>
    <input type="number" id="duration" name="duration" min="0" value="<?= htmlspecialchars($duration) ?>">

    <button type="submit">Ajouter à la playlist</button>
</form>

<p>
    <a href="../playlists/view.php?id=<?= $playlistId ?>"><button>Retour à la playlist</button></a>
</p>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
