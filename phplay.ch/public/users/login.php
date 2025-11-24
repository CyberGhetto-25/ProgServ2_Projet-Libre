<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/lang.php';   // démarre la session + traductions
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = __("login_title") ?? "Connexion";

$email = '';
$errors = [];

// Si l'utilisateur est déjà connecté, on peut le renvoyer à l'accueil
if (is_logged_in()) {
    header('Location: /index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = __("required_field") ?? "Tous les champs sont obligatoires.";
    }

    if (empty($errors)) {
        // Récupérer l'utilisateur par email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérifier mot de passe
        if ($user && !empty($user['password_hash']) && password_verify($password, $user['password_hash'])) {
            login_user($user);
            header('Location: /index.php');
            exit;
        } else {
            $errors[] = __("login_invalid_credentials") ?? "Email ou mot de passe incorrect.";
        }
    }
}

// Affichage HTML
require_once __DIR__ . '/../includes/header.php';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.lime.min.css">
    <title><?= htmlspecialchars($pageTitle) ?></title>
<main>
    <h1><?= htmlspecialchars($pageTitle) ?></h1>

    <?php if (!empty($errors)) : ?>
        <ul style="color:red;">
            <?php foreach ($errors as $error) : ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" action="/users/login.php">
        <div>
            <label for="email"><?= __("email") ?? "Email" ?></label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
        </div>

        <div>
            <label for="password"><?= __("password") ?? "Mot de passe" ?></label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit"><?= __("login_button") ?? "Se connecter" ?></button>
    </form>

    <p>
        <a href="/"><?= __("back_home") ?? "Retour à l'accueil" ?></a>
    </p>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</main>
</body>
</html>