<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';

if (!is_logged_in()) {
    header("Location: ../users/login.php");
    exit();
}

$pageTitle = __("create_playlist_title");

$playlistName = '';
$isPublic = 0;
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $playlistName = trim($_POST["playlist-name"] ?? '');
    $isPublic = isset($_POST["is-public"]) ? 1 : 0;

    if (strlen($playlistName) < 2) $errors[] = __("playlist_name_error");

    if (empty($errors)) {
        $currentUser = current_user();
        $stmt = $pdo->prepare("INSERT INTO playlists (user_id, playlist_name, is_public) 
                               VALUES (:user_id, :playlist_name, :is_public)");
        $stmt->execute([
            ':user_id' => $currentUser['id'],
            ':playlist_name' => $playlistName,
            ':is_public' => $isPublic
        ]);

        header("Location: ../index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.lime.min.css">
    <title><?= htmlspecialchars($pageTitle) ?></title>
</head>
<body>
<main class="container">
    <h1><?= __("create_playlist_heading") ?></h1>

    <?php if ($_SERVER["REQUEST_METHOD"] === "POST"): ?>
        <?php if (empty($errors)): ?>
            <p style="color: green;"><?= __("playlist_created") ?></p>
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
        <label for="playlist-name"><?= __("playlist_name_label") ?></label>
        <input type="text" id="playlist-name" name="playlist-name" value="<?= htmlspecialchars($playlistName) ?>" required minlength="2">

        <label>
            <input type="checkbox" name="is-public" <?= !empty($isPublic) ? 'checked' : '' ?>>
            <?= __("is_public_label") ?>
        </label>

        <button type="submit"><?= __("save_playlist_button") ?></button>
    </form>

    <a href="../index.php"><button><?= __("back_home") ?></button></a>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</main>
</body>
</html>