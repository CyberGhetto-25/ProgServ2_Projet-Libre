<?php
const DATABASE_CONFIGURATION_FILE = __DIR__ . '/../src/config/database.ini';

// Documentation : https://www.php.net/manual/fr/function.parse-ini-file.php
$config = parse_ini_file(DATABASE_CONFIGURATION_FILE, true);

if (!$config) {
    throw new Exception("Erreur lors de la lecture du fichier de configuration : " . DATABASE_CONFIGURATION_FILE);
}

$host = $config['host'];
$port = $config['port'];
$database = $config['database'];
$username = $config['username'];
$password = $config['password'];

// Documentation :
//   - https://www.php.net/manual/fr/pdo.connections.php
//   - https://www.php.net/manual/fr/ref.pdo-mysql.connection.php
$pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $username, $password);

// Création de la base de données si elle n'existe pas
$sql = "CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Sélection de la base de données
$sql = "USE `$database`;";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Création de la table `users` si elle n'existe pas
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    age INT NOT NULL
);";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Création de la table `playlists` si elle n'existe pas
$sql = "CREATE TABLE IF NOT EXISTS playlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    playlist_name VARCHAR(255) NOT NULL,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Création de la table `tracks` si elle n'existe pas
$sql = "CREATE TABLE IF NOT EXISTS tracks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    artist VARCHAR(255) NOT NULL,
    genre VARCHAR(100),
    duration INT, -- Durée en secondes
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Définition de la requête SQL pour récupérer tous les utilisateurs
$sql = "SELECT * FROM users";

// Préparation de la requête SQL
$stmt = $pdo->prepare($sql);

// Exécution de la requête SQL
$stmt->execute();

// Récupération de tous les utilisateurs
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

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">

    <title>Gestion de mes morceaux | PHPlay</title>
</head>

<body>
    <main class="container">
        <h1>PhPlay - Accueil</h1>

        <h2>🎧 PhPlay</h2>

        <p><a href="create.php"><button>👤 Nouvel utilisateur</button></a></p>
        <p><a href="create_playlist.php"><button>➕ Nouvelle playlist</button></a></p>

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
                            <a href="playlist.php?id=<?= $playlist['id'] ?>"><button>🎵 Voir</button></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</main>
</body>
</html>
