<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
$pageTitle = __("create_playlist_title");

// Récupération des utilisateurs
$stmt = $pdo->query("SELECT * FROM users ORDER BY first_name, last_name");
$users = $stmt->fetchAll();

$playlistName = '';
$userId = '';
$isPublic = 0;
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $playlistName = trim($_POST["playlist-name"] ?? '');
    $userId = $_POST["user-id"] ?? '';
    $isPublic = isset($_POST["is-public"]) ? 1 : 0;

    if (strlen($playlistName) < 2) $errors[] = __("playlist_name_error");
    if (empty($userId)) $errors[] = __("user_required_error");

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO playlists (user_id, playlist_name, is_public) 
                               VALUES (:user_id, :playlist_name, :is_public)");
        $stmt->execute([
            ':user_id' => $userId,
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
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

        <label for="user-id"><?= __("user_label") ?></label>
        <select id="user-id" name="user-id" required>
            <option value=""><?= __("select_user") ?></option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['id'] ?>" <?= ($userId == $user['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

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
