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

$stmtTracks = $pdo->query("SELECT id, title, artist, genre, duration FROM tracks ORDER BY title ASC");
$existingTracks = $stmtTracks->fetchAll();

$title = $artist = $genre = $duration = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mode = $_POST['mode'] ?? 'new';
    
    if ($mode === 'existing') {
        $existingTrackId = $_POST['existing_track_id'] ?? null;
        
        if (!$existingTrackId) {
            $errors[] = __("select_track_error");
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM playlist_tracks WHERE playlist_id = :playlist_id AND track_id = :track_id");
            $stmt->execute([':playlist_id' => $playlistId, ':track_id' => $existingTrackId]);
            
            if ($stmt->fetchColumn() > 0) {
                $errors[] = __("track_already_in_playlist");
            } else {
                $stmt = $pdo->prepare("INSERT INTO playlist_tracks (playlist_id, track_id) VALUES (:playlist_id, :track_id)");
                $stmt->execute([':playlist_id' => $playlistId, ':track_id' => $existingTrackId]);
                
                header("Location: ../playlist/view.php?id=$playlistId");
                exit();
            }
        }
    } else {
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
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.lime.min.css">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <style>
        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid var(--primary);
        }
        .tab-button {
            padding: 0.5rem 1rem;
            border: none;
            background: none;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        .tab-button.active {
            border-bottom-color: var(--primary);
            font-weight: bold;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .track-item {
            padding: 0.75rem;
            border: 1px solid var(--form-element-border-color);
            border-radius: var(--border-radius);
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .track-item:hover {
            background-color: var(--primary-focus);
        }
        .track-item input[type="radio"] {
            margin-right: 0.5rem;
        }
        .track-info {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        .track-title {
            font-weight: bold;
        }
        .track-details {
            font-size: 0.9em;
            opacity: 0.8;
        }
        .search-box {
            margin-bottom: 1rem;
        }
    </style>
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

    <div class="tabs">
        <button class="tab-button active" onclick="switchTab('existing')">
            <?= __("select_existing_track") ?>
        </button>
        <button class="tab-button" onclick="switchTab('new')">
            <?= __("create_new_track") ?>
        </button>
    </div>

    <div id="existing-tab" class="tab-content active">
        <form method="POST">
            <input type="hidden" name="mode" value="existing">
            
            <div class="search-box">
                <input type="text" id="search" placeholder="<?= __("search_track") ?>" onkeyup="filterTracks()">
            </div>

            <div id="tracks-list">
                <?php if (empty($existingTracks)): ?>
                    <p><?= __("no_tracks_available") ?></p>
                <?php else: ?>
                    <?php foreach ($existingTracks as $track): ?>
                        <label class="track-item" data-title="<?= strtolower(htmlspecialchars($track['title'])) ?>" 
                               data-artist="<?= strtolower(htmlspecialchars($track['artist'])) ?>">
                            <input type="radio" name="existing_track_id" value="<?= $track['id'] ?>" required>
                            <div class="track-info">
                                <span class="track-title"><?= htmlspecialchars($track['title']) ?></span>
                                <span class="track-details">
                                    <?= htmlspecialchars($track['artist']) ?>
                                    <?php if ($track['genre']): ?>
                                        • <?= htmlspecialchars($track['genre']) ?>
                                    <?php endif; ?>
                                    <?php if ($track['duration']): ?>
                                        • <?= $track['duration'] ?>s
                                    <?php endif; ?>
                                </span>
                            </div>
                        </label>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if (!empty($existingTracks)): ?>
                <button type="submit"><?= __("add_selected_track") ?></button>
            <?php endif; ?>
        </form>
    </div>

    <div id="new-tab" class="tab-content">
        <form method="POST">
            <input type="hidden" name="mode" value="new">
            
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
    </div>

    <p>
        <a href="../playlist/view.php?id=<?= $playlistId ?>"><button><?= __("back_to_playlist") ?></button></a>
    </p>

    <script>
        function switchTab(tab) {
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });

            document.getElementById(tab + '-tab').classList.add('active');
            event.target.classList.add('active');
        }

        function filterTracks() {
            const searchTerm = document.getElementById('search').value.toLowerCase();
            const tracks = document.querySelectorAll('.track-item');

            tracks.forEach(track => {
                const title = track.getAttribute('data-title');
                const artist = track.getAttribute('data-artist');
                
                if (title.includes(searchTerm) || artist.includes(searchTerm)) {
                    track.style.display = 'block';
                } else {
                    track.style.display = 'none';
                }
            });
        }
    </script>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</main>
</body>
</html>