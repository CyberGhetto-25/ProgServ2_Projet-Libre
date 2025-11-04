<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
$pageTitle = __("add_track_title");

$playlistId = $_GET['playlist_id'] ?? null;
if (!$playlistId) die("Aucune playlist spécifiée.");

$stmt = $pdo->prepare("SELECT * FROM playlists WHERE id = :id");
$stmt->execute([':id' => $playlistId]);
$playlist = $stmt->fetch();

if (!$playlist) die("Playlist introuvable.");

// Variables
$title = $artist = $genre = $duration = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? '');
    $artist = trim($_POST["artist"] ?? '');
    $genre = trim($_POST["genre"] ?? '');
    $duration = trim($_POST["duration"] ?? '');

    if (strlen($title) < 2) $errors[] = __("title_error");
    if (strlen($artist) < 2) $errors[] = __("artist_error");
    if ($duration !== '' && (!ctype_digit($duration) || $duration < 0)) $errors[] = __("duration_error");

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO tracks (title, artist, genre, duration) VALUES (:title, :artist, :genre, :duration)");
        $stmt->execute([
            ':title' => $title,
            ':artist' => $artist,
            ':genre' => $genre ?: null,
            ':duration' => $duration ?: null
        ]);

        $trackId = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO playlist_tracks (playlist_id, track_id) VALUES (:playlist_id, :track_id)");
        $stmt->execute([':playlist_id' => $playlistId, ':track_id' => $trackId]);

        header("Location: ../playlist/view.php?id=$playlistId");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <title><?= htmlspecialchars($pageTitle) ?></title>
</head>

<body>
<main class="container">
    <h1><?= __("add_track_heading") ?></h1>
    <h3><?= htmlspecialchars($playlist['playlist_name']) ?></h3>

    <?php if ($_SERVER["REQUEST_METHOD"] === "POST"): ?>
        <?php if (empty($errors)): ?>
            <p style="color: green;"><?= __("track_added") ?></p>
        <?php else: ?>
            <p style="color: red;"><?= __("error_message") ?></p>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>

    <form method="POST">
        <label for="title"><?= __("track_title_label") ?></label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required minlength="2">

        <label for="artist"><?= __("track_artist_label") ?></label>
        <input type="text" id="artist" name="artist" value="<?= htmlspecialchars($artist) ?>" required minlength="2">

        <label for="genre"><?= __("track_genre_label") ?></label>
        <input type="text" id="genre" name="genre" value="<?= htmlspecialchars($genre) ?>">

        <label for="duration"><?= __("track_duration_label") ?></label>
        <input type="number" id="duration" name="duration" min="0" value="<?= htmlspecialchars($duration) ?>">

        <button type="submit"><?= __("add_track_button") ?></button>
    </form>

    <p>
        <a href="../playlist/view.php?id=<?= $playlistId ?>"><button><?= __("back_to_playlist") ?></button></a>
    </p>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</main>
</body>
</html>
