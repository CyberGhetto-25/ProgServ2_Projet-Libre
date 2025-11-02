<?php
$pageTitle = "Créer une playlist | PHPlay";
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/header.php';

// Récupération des utilisateurs
$sql = "SELECT * FROM users ORDER BY first_name, last_name";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll();

// Gestion du formulaire
$playlistName = '';
$userId = '';
$isPublic = 0;
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $playlistName = $_POST["playlist-name"] ?? '';
    $userId = $_POST["user-id"] ?? '';
    $isPublic = isset($_POST["is-public"]) ? 1 : 0;

    if (empty($playlistName) || strlen($playlistName) < 2) {
        $errors[] = "Le nom de la playlist doit contenir au moins 2 caractères.";
    }
    if (empty($userId)) {
        $errors[] = "Vous devez sélectionner un utilisateur.";
    }

    if (empty($errors)) {
        $sql = "INSERT INTO playlists (user_id, playlist_name, is_public) VALUES (:user_id, :playlist_name, :is_public)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':playlist_name', $playlistName);
        $stmt->bindValue(':is_public', $isPublic);
        $stmt->execute();

        header("Location: ../index.php");
        exit();
    }
}
?>

<h1>Créer une nouvelle playlist</h1>
<?php if ($_SERVER["REQUEST_METHOD"] === "POST"): ?>
    <?php if (empty($errors)): ?>
        <p style="color: green;">La playlist a été créée avec succès 🎵</p>
    <?php else: ?>
        <p style="color: red;">Le formulaire contient des erreurs :</p>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
<?php endif; ?>

<form action="create.php" method="POST">
    <label for="playlist-name">Nom de la playlist</label>
    <input type="text" id="playlist-name" name="playlist-name" value="<?= htmlspecialchars($playlistName) ?>" required minlength="2">

    <label for="user-id">Utilisateur</label>
    <select id="user-id" name="user-id" required>
        <option value="">-- Sélectionnez un utilisateur --</option>
        <?php foreach ($users as $user): ?>
            <option value="<?= $user['id'] ?>" <?= (isset($userId) && $userId == $user['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>
        <input type="checkbox" name="is-public" <?= !empty($isPublic) ? 'checked' : '' ?>>
        Playlist publique
    </label>

    <button type="submit">Créer</button>
</form>

<p><a href="../index.php">⬅ Retour à l’accueil</a></p>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
