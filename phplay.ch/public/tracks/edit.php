<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';

$trackId = $_GET['id'] ?? null;
$playlistId = $_GET['playlist_id'] ?? null;

if (!$trackId) die("Morceau non spécifié.");

// Récupérer les données du morceau
$stmt = $pdo->prepare("SELECT * FROM tracks WHERE id = :id");
$stmt->execute([':id' => $trackId]);
$track = $stmt->fetch();

if (!$track) die("Morceau introuvable.");

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $playlistId = $_POST["playlist_id"] ?? null;
    $title = trim($_POST["title"] ?? '');
    $artist = trim($_POST["artist"] ?? '');
    $genre = trim($_POST["genre"] ?? '');
    $duration = $_POST["duration"] ?? null;

    if (strlen($title) < 2) $errors[] = __("title_error");
    if (strlen($artist) < 2) $errors[] = __("artist_error");

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE tracks SET title = :title, artist = :artist, genre = :genre, duration = :duration WHERE id = :id");
        $stmt->execute([
            ':title' => $title,
            ':artist' => $artist,
            ':genre' => $genre,
            ':duration' => $duration,
            ':id' => $trackId
        ]);

        header("Location: ../playlist/view.php?id=$playlistId");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.lime.min.css">
    <title><?= __("edit") ?> - <?= htmlspecialchars($track['title']) ?></title>
</head>
<body>
<main class="container">
    <h1><?= __("edit") ?> : <?= htmlspecialchars($track['title']) ?></h1>

    <form method="POST">
        <input type="hidden" name="playlist_id" value="<?= htmlspecialchars($playlistId) ?>">
        
        <label><?= __("track_title_label") ?>
            <input type="text" name="title" value="<?= htmlspecialchars($track['title']) ?>" required>
        </label>
        <label><?= __("track_artist_label") ?>
            <input type="text" name="artist" value="<?= htmlspecialchars($track['artist']) ?>" required>
        </label>
        <label><?= __("track_genre_label") ?>
            <input type="text" name="genre" value="<?= htmlspecialchars($track['genre']) ?>">
        </label>
        <label><?= __("track_duration_label") ?>
            <input type="number" name="duration" value="<?= htmlspecialchars($track['duration']) ?>">
        </label>

        <div class="grid">
            <button type="submit"><?= __("save") ?></button>
            <a href="../playlist/view.php?id=<?= $playlistId ?>" role="button" class="secondary"><?= __("cancel") ?></a>
        </div>
    </form>
</main>
</body>
</html>