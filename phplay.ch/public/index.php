<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <title><?= $pageTitle ?? 'PHPlay' ?></title>
</head>

<body>
    <main class="container">

        <?php
        $pageTitle = "Accueil | PHPlay";
        require_once __DIR__ . '/includes/config.php';
        require_once __DIR__ . '/includes/header.php';

        // Récupération des utilisateurs
        $sql = "SELECT * FROM users";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll();

        // Récupération des playlists
        $sql = "SELECT p.*, u.first_name, u.last_name
            FROM playlists p
            JOIN users u ON p.user_id = u.id
            ORDER BY p.created_at DESC;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $playlists = $stmt->fetchAll();
        ?>

        <h1>PhPlay - Accueil</h1>
        <h2>🎧 PhPlay</h2>

        <p>
            <a href="users/create.php"><button>👤 Nouvel utilisateur</button></a>
            <a href="playlist/create.php"><button>➕ Nouvelle playlist</button></a>
        </p>

        <h2>Utilisateurs</h2>
        <?php if (count($users) === 0): ?>
            <p>Aucun utilisateur pour le moment.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Prénom</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Âge</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['first_name']) ?></td>
                            <td><?= htmlspecialchars($user['last_name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['age']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <h2>Playlists</h2>
        <?php if (count($playlists) === 0): ?>
            <p>Aucune playlist pour le moment.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Nom de la playlist</th>
                        <th>Créée par</th>
                        <th>Visibilité</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($playlists as $playlist): ?>
                        <tr>
                            <td><?= htmlspecialchars($playlist['playlist_name']) ?></td>
                            <td><?= htmlspecialchars($playlist['first_name'] . ' ' . $playlist['last_name']) ?></td>
                            <td><?= $playlist['is_public'] ? 'Publique' : 'Privée' ?></td>
                            <td>
                                <a href="playlist/view.php?id=<?= $playlist['id'] ?>"><button>🎵 Voir</button></a>
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