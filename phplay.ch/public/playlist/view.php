<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
$pageTitle = __("playlist_title");
$playlistId = $_GET['id'] ?? null;
if (!$playlistId) die("Aucune playlist spécifiée.");

$sql = "SELECT p.*, u.first_name, u.last_name FROM playlists p 
        JOIN users u ON u.id = p.user_id WHERE p.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $playlistId]);
$playlist = $stmt->fetch();

if (!$playlist) die("Playlist introuvable.");

$sql = "SELECT t.* FROM tracks t 
        JOIN playlist_tracks pt ON t.id = pt.track_id 
        WHERE pt.playlist_id = :id ORDER BY t.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $playlistId]);
$tracks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.lime.min.css">
    <title><?= sprintf(__("playlist_title"), htmlspecialchars($playlist['playlist_name'])) ?></title>
    <style>
        .filter-box {
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
<main class="container">
    <h1><?= htmlspecialchars($playlist['playlist_name']) ?></h1>
    <p>
        <?= __("created_by") ?> : <?= htmlspecialchars($playlist['first_name'] . ' ' . $playlist['last_name']) ?><br>
        <?= __("visibility") ?> : <?= $playlist['is_public'] ? __("public") : __("private") ?>
    </p>

    <p>
        <a href="../index.php"><button><?= __("back_home") ?></button></a>
        <a href="../tracks/create.php?playlist_id=<?= $playlistId ?>"><button><?= __("add_track_button") ?></button></a>
    </p>

    <h2><?= __("tracks_section") ?></h2>
    <?php if (count($tracks) === 0): ?>
        <p><?= __("no_tracks") ?></p>
    <?php else: ?>
        <div class="filter-box">
            <input type="text" id="filterInput" placeholder="<?= __("filter_tracks") ?>" onkeyup="filterTable()">
        </div>
        <table id="tracksTable">
            <thead>
            <tr>
                <th><?= __("track_title") ?></th>
                <th><?= __("track_artist") ?></th>
                <th><?= __("track_genre") ?></th>
                <th><?= __("track_duration") ?></th>
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

    <script>
        function filterTable() {
            const input = document.getElementById('filterInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('tracksTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
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
    </script>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</main>
</body>
</html>