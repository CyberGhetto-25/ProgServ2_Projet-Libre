<?php
const DATABASE_CONFIGURATION_FILE = __DIR__ . '/../src/config/database.ini';

// Lecture de la config
$config = parse_ini_file(DATABASE_CONFIGURATION_FILE, true);
if (!$config) {
    throw new Exception("Erreur lors de la lecture du fichier de configuration : " . DATABASE_CONFIGURATION_FILE);
}

$host = $config['host'];
$port = $config['port'];
$database = $config['database'];
$username = $config['username'];
$password = $config['password'];

// Connexion PDO
$pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $username, $password);

// Création de la base si besoin
$sql = "CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Sélection de la base
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

// Récupération des utilisateurs pour le <select>
$sql = "SELECT * FROM users ORDER BY first_name, last_name";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll();

// Gestion du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $playlistName = $_POST["playlist-name"] ?? '';
    $userId = $_POST["user-id"] ?? '';
    $isPublic = isset($_POST["is-public"]) ? 1 : 0;

    $errors = [];

    if (empty($playlistName) || strlen($playlistName) < 2) {
        $errors[] = "Le nom de la playlist doit contenir au moins 2 caractères.";
    }

    if (empty($userId)) {
        $errors[] = "Vous devez sélectionner un utilisateur.";
    }

    if (empty($errors)) {
        // Insertion de la playlist
        $sql = "INSERT INTO playlists (
            user_id,
            playlist_name,
            is_public
        ) VALUES (
            :user_id,
            :playlist_name,
            :is_public
        )";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':playlist_name', $playlistName);
        $stmt->bindValue(':is_public', $isPublic);
        $stmt->execute();

        // Redirection : vers l'accueil (comme create.php). 
        // Si tu as déjà playlist.php, tu peux plutôt rediriger vers la page de la playlist :
        // header("Location: ../playlist.php?id=" . $pdo->lastInsertId()); exit();
        header("Location: ../index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">

    <title>Créer une playlist | PHPlay</title>
</head>
<body>
<main class="container">
    <h1>Créer une nouvelle playlist</h1>

    <?php if ($_SERVER["REQUEST_METHOD"] === "POST") { ?>
        <?php if (empty($errors)) { ?>
            <p style="color: green;">La playlist a été créée avec succès 🎵</p>
        <?php } else { ?>
            <p style="color: red;">Le formulaire contient des erreurs :</p>
            <ul>
                <?php foreach ($errors as $error) { ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php } ?>
            </ul>
        <?php } ?>
    <?php } ?>

    <form action="create_playlist.php" method="POST">
        <label for="playlist-name">Nom de la playlist</label>
        <input
            type="text"
            id="playlist-name"
            name="playlist-name"
            value="<?= htmlspecialchars($playlistName ?? '') ?>"
            required
            minlength="2"
        >

        <label for="user-id">Utilisateur</label>
        <select id="user-id" name="user-id" required>
            <option value="">-- Sélectionnez un utilisateur --</option>
            <?php foreach ($users as $user) { ?>
                <option value="<?= $user['id'] ?>"
                    <?= (isset($userId) && $userId == $user['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                </option>
            <?php } ?>
        </select>

        <label for="is-public">
            <input type="checkbox" id="is-public" name="is-public" <?= !empty($isPublic) ? 'checked' : '' ?>>
            Playlist publique
        </label>

        <button type="submit">Créer</button>
    </form>

    <p><a href="../index.php">⬅ Retour à l’accueil</a></p>
</main>
</body>
</html>
