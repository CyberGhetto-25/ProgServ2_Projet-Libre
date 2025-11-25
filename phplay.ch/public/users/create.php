<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/lang.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../../src/utils/autoloader.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

const MAIL_CONFIGURATION_FILE = __DIR__ . '/../../src/config/mail.ini';


$pageTitle = __("create_user_title") ?? "Créer un utilisateur";

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
                ':password_hash' => $passwordHash,
            ]);

            $config = parse_ini_file(MAIL_CONFIGURATION_FILE, true);

            if (!$config) {
                throw new Exception("Erreur lors de la lecture du fichier de configuration : " . MAIL_CONFIGURATION_FILE);
            }

            $host = $config['host'];
            $port = filter_var($config['port'], FILTER_VALIDATE_INT);
            $authentication = filter_var($config['authentication'], FILTER_VALIDATE_BOOLEAN);
            $username = $config['username'];
            $mailPassword = $config['password'];
            $from_email = $config['from_email'];
            $from_name = $config['from_name'];

            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = $host;
                $mail->Port = $port;
                $mail->SMTPAuth = $authentication;
                if ($authentication) {
                    if ($port === 465) {
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    } else {
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    }
                    $mail->Username = $username;
                    $mail->Password = $mailPassword;
                }
                $mail->CharSet = "UTF-8";
                $mail->Encoding = "base64";

                $mail->setFrom($from_email, $from_name);
                $mail->addAddress($email, $firstName . " " . $lastName);

                $mail->isHTML(true);
                $mail->Subject = 'Bienvenue sur PHPlay !';

                $mail->Body = '
<html>
<body style="margin:0; padding:0; background-color:#f4f4f4; font-family:Arial, sans-serif;">
    <table align="center" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; background:#ffffff; border-radius:8px; padding:20px;">
        <tr>
            <td style="text-align:center; padding-bottom:20px;">
                <h2 style="color:#4CAF50; margin:0;">Bienvenue sur PHPlay 🎉</h2>
            </td>
        </tr>

        <tr>
            <td style="font-size:16px; color:#333;">
                <p>Bonjour <strong>' . htmlspecialchars($firstName . " " . $lastName) . '</strong>,</p>
                <p>Nous sommes ravis de vous compter parmi les nouveaux utilisateurs de notre plateforme.</p>
                <p>Votre compte a bien été créé et vous pouvez désormais vous connecter et utiliser tous nos services.</p>
                
                <p style="margin-top:25px;">Si vous avez la moindre question, n’hésitez pas à nous contacter.</p>

                <p style="margin-top:30px; font-size:14px; color:#777;">
                    À très bientôt,<br>
                    <strong>L\'équipe PHPlay</strong>
                </p>
            </td>
        </tr>

        <tr>
            <td style="text-align:center; padding-top:20px; color:#aaa; font-size:12px;">
                © ' . date("Y") . ' PHPlay — Tous droits réservés
            </td>
        </tr>
    </table>
</body>
</html>
';

                $mail->AltBody =
                    "Bienvenue sur PHPlay !\n\n" .
                    "Bonjour " . $firstName . " " . $lastName . ",\n" .
                    "Votre compte utilisateur a bien été créé.\n\n" .
                    "À bientôt,\n" .
                    "L'équipe PHPlay";

                $mail->send();
            } catch (Exception $e) {
                error_log("Erreur PHPMailer : " . $mail->ErrorInfo);
            }

            header('Location: /index.php');
            exit;
        } catch (PDOException $e) {
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
</head>
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