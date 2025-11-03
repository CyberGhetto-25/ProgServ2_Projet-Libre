<?php
$pageTitle = "Créer un utilisateur | PHPlay";
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';

// Gestion du formulaire
$firstName = '';
$lastName = '';
$email = '';
$age = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = $_POST["first-name"] ?? '';
    $lastName = $_POST["last-name"] ?? '';
    $email = $_POST["email"] ?? '';
    $age = $_POST["age"] ?? '';

    if (empty($firstName) || strlen($firstName) < 2) {
        $errors[] = "Le prénom doit contenir au moins 2 caractères.";
    }
    if (empty($lastName) || strlen($lastName) < 2) {
        $errors[] = "Le nom doit contenir au moins 2 caractères.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Un email valide est requis.";
    }
    if ($age < 0) {
        $errors[] = "L'âge doit être un nombre positif.";
    }

    if (empty($errors)) {
        $sql = "INSERT INTO users (first_name, last_name, email, age) VALUES (:first_name, :last_name, :email, :age)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':first_name', $firstName);
        $stmt->bindValue(':last_name', $lastName);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':age', $age);
        $stmt->execute();

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

    <title>Créer un.e nouvel.le utilisateur.trice | PHPlay</title>
</head>

<body>
    <main class="container">
        <?php if ($_SERVER["REQUEST_METHOD"] === "POST"): ?>
            <?php if (empty($errors)): ?>
                <p style="color: green;">Le formulaire a été soumis avec succès !</p>
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
            <label for="first-name">Prénom</label>
            <input type="text" id="first-name" name="first-name" value="<?= htmlspecialchars($firstName) ?>" required minlength="2">

            <label for="last-name">Nom</label>
            <input type="text" id="last-name" name="last-name" value="<?= htmlspecialchars($lastName) ?>" required minlength="2">

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

            <label for="age">Âge</label>
            <input type="number" id="age" name="age" value="<?= htmlspecialchars($age) ?>" required min="0">

            <button type="submit">Créer</button>
        </form>

        <?php require_once __DIR__ . '/../includes/footer.php'; ?>

    </main>

</body>