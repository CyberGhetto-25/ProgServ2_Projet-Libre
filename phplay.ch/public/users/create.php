<?php
$pageTitle = __("create_user_title");
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';

// Gestion du formulaire
$firstName = '';
$lastName = '';
$email = '';
$age = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = trim($_POST["first-name"] ?? '');
    $lastName = trim($_POST["last-name"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $age = trim($_POST["age"] ?? '');

    if (empty($firstName) || strlen($firstName) < 2) {
        $errors[] = __("first_name_error");
    }
    if (empty($lastName) || strlen($lastName) < 2) {
        $errors[] = __("last_name_error");
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = __("email_error");
    }
    if ($age < 0) {
        $errors[] = __("age_error");
    }

    if (empty($errors)) {
        $sql = "INSERT INTO users (first_name, last_name, email, age) 
                VALUES (:first_name, :last_name, :email, :age)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':email' => $email,
            ':age' => $age
        ]);

        header("Location: ../index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <title><?= htmlspecialchars($pageTitle) ?></title>
</head>
<body>
    <main class="container">
        <h1><?= __("create_user_heading") ?></h1>

        <?php if ($_SERVER["REQUEST_METHOD"] === "POST"): ?>
            <?php if (empty($errors)): ?>
                <p style="color: green;"><?= __("success_message") ?></p>
            <?php else: ?>
                <p style="color: red;"><?= __("error_message") ?></p>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        <?php endif; ?>

        <form method="POST">
            <label for="first-name"><?= __("first_name") ?></label>
            <input type="text" id="first-name" name="first-name" value="<?= htmlspecialchars($firstName) ?>" required minlength="2">

            <label for="last-name"><?= __("last_name") ?></label>
            <input type="text" id="last-name" name="last-name" value="<?= htmlspecialchars($lastName) ?>" required minlength="2">

            <label for="email"><?= __("email") ?></label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

            <label for="age"><?= __("age") ?></label>
            <input type="number" id="age" name="age" value="<?= htmlspecialchars($age) ?>" required min="0">

            <button type="submit"><?= __("create_button") ?></button>
        </form>

        <?php require_once __DIR__ . '/../includes/footer.php'; ?>
    </main>
</body>
</html>
