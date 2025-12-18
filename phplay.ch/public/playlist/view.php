<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';

// Récupération de l'ID de la playlist
$playlistId = $_GET['id'] ?? null;
if (!$playlistId) {
    die("Aucune playlist spécifiée.");
}

// 1. Récupération des informations de la playlist
$sql = "SELECT p.*, u.first_name, u.last_name FROM playlists p 
        JOIN users u ON u.id = p.user_id WHERE p.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $playlistId]);
$playlist = $stmt->fetch();

if (!$playlist) {
    $_SESSION['error_message'] = __("playlist_not_found") ?? "Playlist introuvable.";
    header("Location: ../index.php");
    exit();
}

$currentUser = current_user();

// Vérification de la visibilité (Sécurité)
$isPublic = (int)($playlist['is_public'] ?? 0) === 1;
$isOwner  = $currentUser && (int)$currentUser['id'] === (int)$playlist['user_id'];

if (!$isPublic && !is_superadmin() && !$isOwner) {

    // si pas loggé -> on l'envoie se connecté
    if (!$currentUser) {
        header("Location: /users/login.php");
        exit();
    }

    // si loggé mais pas owner -> interdit
    http_response_code(403);
    exit("403 Forbidden");
}

// 2. Récupération des morceaux associés
$sql = "SELECT t.* FROM tracks t 
        JOIN playlist_tracks pt ON t.id = pt.track_id 
        WHERE pt.playlist_id = :id ORDER BY t.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $playlistId]);
$tracks = $stmt->fetchAll();

$pageTitle = sprintf(__("playlist_title"), htmlspecialchars($playlist['playlist_name']));
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.lime.min.css">
    <title><?= $pageTitle ?></title>
    <style>
        /* Styles fusionnés */
        .actions-group,
        .filter-box {
            margin-bottom: 2rem;
        }

        .btn-delete {
            background-color: #d32f2f;
            border-color: #d32f2f;
        }

        .btn-edit {
            background-color: #f57c00;
            border-color: #f57c00;
        }
    </style>
</head>

<body>
    <main class="container">
        <header>
            <h1><?= htmlspecialchars($playlist['playlist_name']) ?></h1>
            <p>
                <strong><?= __("created_by") ?> :</strong> <?= htmlspecialchars($playlist['first_name'] . ' ' . $playlist['last_name']) ?><br>
                <strong><?= __("visibility") ?> :</strong> <?= $playlist['is_public'] ? __("public") : __("private") ?>
            </p>
        </header>

        <div class="actions-group">
            <div class="grid">
                <a href="../index.php" role="button" class="secondary"><?= __("back_home") ?></a>
                <a href="../tracks/create.php?playlist_id=<?= $playlistId ?>" role="button"><?= __("add_track_button") ?></a>

                <a href="edit.php?id=<?= $playlistId ?>" role="button" class="btn-edit"><?= __("edit") ?></a>

                <a href="delete.php?id=<?= $playlistId ?>"
                    role="button"
                    class="btn-delete"
                    onclick="return confirm('<?= __('confirm_delete_playlist') ?? 'Êtes-vous sûr de vouloir supprimer cette playlist ?' ?>')">
                    <?= __('delete') ?>
                </a>
            </div>
        </div>

        <hr>

        <section>
            <h2><?= __("tracks_section") ?></h2>

            <?php if (count($tracks) === 0): ?>
                <article>
                    <p><?= __("no_tracks") ?></p>
                </article>
            <?php else: ?>
                <div class="filter-box">
                    <input type="text" id="filterInput" placeholder="<?= __("filter_tracks") ?>" onkeyup="filterTable()">
                </div>

                <table id="tracksTable" class="striped">
                    <thead>
                        <tr>
                            <th><?= __("track_title") ?></th>
                            <th><?= __("track_artist") ?></th>
                            <th><?= __("track_genre") ?></th>
                            <th><?= __("track_duration") ?></th>
                            <th><?= __("actions") ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tracks as $track): ?>
                            <tr>
                                <td><?= htmlspecialchars($track['title']) ?></td>
                                <td><?= htmlspecialchars($track['artist']) ?></td>
                                <td><?= htmlspecialchars($track['genre'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($track['duration'] ?? '-') ?> s</td>
                                <td>
                                    <a href="../tracks/edit.php?id=<?= $track['id'] ?>&playlist_id=<?= $playlistId ?>"
                                        data-tooltip="<?= __("edit") ?>">
                                        <?= __("edit") ?>
                                    </a>
                                    &nbsp;
                                    <a href="../tracks/delete.php?id=<?= $track['id'] ?>&playlist_id=<?= $playlistId ?>"
                                        style="color: red;"
                                        onclick="return confirm('<?= __("confirm_delete_track") ?? "Retirer ce morceau de la playlist ?" ?>')"
                                        data-tooltip="<?= __("delete") ?>">
                                        <?= __("delete") ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <script>
            function filterTable() {
                const input = document.getElementById('filterInput');
                const filter = input.value.toLowerCase();
                const table = document.getElementById('tracksTable');
                const rows = table.getElementsByTagName('tr');

                for (let i = 1; i < rows.length; i++) {
                    const cells = rows[i].getElementsByTagName('td');
                    if (cells.length > 0) {
                        const title = cells[0].textContent.toLowerCase();
                        const artist = cells[1].textContent.toLowerCase();
                        const genre = cells[2].textContent.toLowerCase();

                        if (title.includes(filter) || artist.includes(filter) || genre.includes(filter)) {
                            rows[i].style.display = '';
                        } else {
                            rows[i].style.display = 'none';
                        }
                    }
                }
            }
        </script>

        <?php require_once __DIR__ . '/../includes/footer.php'; ?>
    </main>
</body>

</html>