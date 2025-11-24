<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/lang.php';
require_once __DIR__ . '/../includes/header.php';

$pageTitle = __("create_user_title") ?? "Créer un utilisateur";

// Variables du formulaire
$firstName = '';
$lastName  = '';
$email     = '';
$age       = '';
$errors    = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = trim($_POST["first-name"] ?? '');
    $lastName  = trim($_POST["last-name"] ?? '');
    $email     = trim($_POST["email"] ?? '');
    $age       = trim($_POST["age"] ?? '');
    $password  = $_POST["password"] ?? '';
    $passwordConfirm = $_POST["password-confirm"] ?? '';

    // Validations basiques
    if ($firstName === '' || $lastName === '' || $email === '' || $age === '' || $password === '' || $passwordConfirm === '') {
        $errors[] = __("required_field") ?? "Tous les champs sont obligatoires.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = __("invalid_email") ?? "Email invalide.";
    }

    if (!ctype_digit($age) || (int)$age < 0) {
        $errors[] = __("invalid_age") ?? "L'âge doit être un nombre positif.";
    }

    if ($password !== $passwordConfirm) {
        $errors[] = __("password_mismatch") ?? "Les mots de passe ne correspondent pas.";
    }

    if (strlen($password) < 6) {
        $errors[] = __("password_too_short") ?? "Le mot de passe doit contenir au moins 6 caractères.";
    }

    if (empty($errors)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("
                INSERT INTO users (first_name, last_name, email, age, password_hash)
                VALUES (:first_name, :last_name, :email, :age, :password_hash)
            ");
            $stmt->execute([
                ':first_name'   => $firstName,
                ':last_name'    => $lastName,
                ':email'        => $email,
                ':age'          => (int)$age,
                ':password_hash'=> $passwordHash,
            ]);

            // Redirection vers l'accueil ou la liste des users
            header('Location: /index.php');
            exit;

        } catch (PDOException $e) {
            // Par ex. email déjà utilisé
            $errors[] = __("database_error") ?? "Erreur base de données : " . $e->getMessage();
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
<main>
    <h1><?= htmlspecialchars($pageTitle) ?></h1>

    <?php if (!empty($errors)) : ?>
        <ul style="color:red;">
            <?php foreach ($errors as $error) : ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" action="/users/create.php">
        <label for="first-name"><?= __("first_name") ?? "Prénom" ?></label>
        <input type="text" id="first-name" name="first-name" value="<?= htmlspecialchars($firstName) ?>" required>

        <label for="last-name"><?= __("last_name") ?? "Nom" ?></label>
        <input type="text" id="last-name" name="last-name" value="<?= htmlspecialchars($lastName) ?>" required>

        <label for="email"><?= __("email") ?? "Email" ?></label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

        <label for="age"><?= __("age") ?? "Âge" ?></label>
        <input type="number" id="age" name="age" value="<?= htmlspecialchars($age) ?>" required min="0">

        <label for="password"><?= __("password") ?? "Mot de passe" ?></label>
        <input type="password" id="password" name="password" required>

        <label for="password-confirm"><?= __("password_confirm") ?? "Confirmer le mot de passe" ?></label>
        <input type="password" id="password-confirm" name="password-confirm" required>

        <button type="submit"><?= __("create_button") ?? "Créer" ?></button>
    </form>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</main>
</body>
</html>