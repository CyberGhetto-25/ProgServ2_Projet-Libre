<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';

// Vérifier que l'utilisateur est connecté
if (!is_logged_in()) {
    header("Location: ../users/login.php");
    exit();
}

$playlistId = $_GET['id'] ?? null;
if (!$playlistId) {
    header("Location: ../index.php");
    exit();
}

// 1. Récupérer les infos de la playlist
$stmt = $pdo->prepare("SELECT * FROM playlists WHERE id = :id");
$stmt->execute([':id' => $playlistId]);
$playlist = $stmt->fetch();

if (!$playlist) die("Playlist introuvable.");

// 2. Vérifier que l'utilisateur connecté est bien le propriétaire
$currentUser = current_user();
if ($currentUser['id'] != $playlist['user_id']) {
    die("Vous n'avez pas l'autorisation de modifier cette playlist.");
}

$errors = [];

// 3. Traitement de la modification (POST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $playlistName = trim($_POST["playlist-name"] ?? '');
    $isPublic = isset($_POST["is-public"]) ? 1 : 0;

    if (strlen($playlistName) < 2) $errors[] = __("playlist_name_error");

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE playlists SET playlist_name = :name, is_public = :is_public WHERE id = :id");
        $stmt->execute([
            ':name' => $playlistName,
            ':is_public' => $isPublic,
            ':id' => $playlistId
        ]);

        header("Location: view.php?id=" . $playlistId);
        exit();
    }
}

$pageTitle = __("edit_playlist_title") ?? "Modifier la Playlist";
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.lime.min.css">
    <title><?= htmlspecialchars($pageTitle) ?></title>
</head>
<body>
<main class="container">
    <h1><?= __("edit_playlist_heading") ?? "Modifier la Playlist" ?></h1>

    <?php if (!empty($errors)): ?>
        <article style="background-color: #ffeeee; border-color: red;">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </article>
    <?php endif; ?>

    <form method="POST">
        <label for="playlist-name"><?= __("playlist_name_label") ?></label>
        <input type="text" id="playlist-name" name="playlist-name" value="<?= htmlspecialchars($playlist['playlist_name']) ?>" required>

        <label>
            <input type="checkbox" name="is-public" <?= $playlist['is_public'] ? 'checked' : '' ?>>
            <?= __("is_public_label") ?>
        </label>

        <div class="grid">
            <button type="submit"><?= __("update_button") ?? "Mettre à jour" ?></button>
            <a href="view.php?id=<?= $playlistId ?>" class="secondary" role="button"><?= __("cancel_button") ?? "Annuler" ?></a>
        </div>
    </form>
</main>
</body>
</html>