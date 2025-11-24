<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
$pageTitle = __('home_title');

// Récupération des utilisateurs
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll();

// Récupération des playlists
$stmt = $pdo->query("SELECT p.*, u.first_name, u.last_name
                     FROM playlists p
                     JOIN users u ON p.user_id = u.id
                     ORDER BY p.created_at DESC");
$playlists = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.lime.min.css">
    <title><?= $pageTitle ?></title>
</head>
<body>
<main class="container">
    <h1><?= __('home_heading') ?></h1>
    <h2><?= __('home_subheading') ?></h2>

    <p>
        <a href="users/create.php"><button><?= __('new_user_button') ?></button></a>
        <a href="playlist/create.php"><button><?= __('new_playlist_button') ?></button></a>
    </p>

    <h2><?= __('playlists_section') ?></h2>
    <?php if (empty($playlists)): ?>
        <p><?= __('no_playlists') ?></p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th><?= __('playlist_name') ?></th>
                    <th><?= __('created_by') ?></th>
                    <th><?= __('visibility') ?></th>
                    <th><?= __('actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($playlists as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['playlist_name']) ?></td>
                        <td><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?></td>
                        <td><?= $p['is_public'] ? __('public') : __('private') ?></td>
                        <td>
                            <a href="playlist/view.php?id=<?= $p['id'] ?>"><button><?= __('view_playlist') ?></button></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
</main>
</body>
</html>
