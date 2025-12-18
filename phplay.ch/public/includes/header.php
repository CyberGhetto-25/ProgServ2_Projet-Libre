<?php
require_once __DIR__ . '/lang.php';
require_once __DIR__ . '/auth.php';

$currentUser = current_user();

$currentUrl = $_SERVER['REQUEST_URI'];
$parsedUrl = parse_url($currentUrl);
$path = $parsedUrl['path'] ?? '/';
parse_str($parsedUrl['query'] ?? '', $params);

$params['lang'] = 'fr';
$frUrl = $path . '?' . http_build_query($params);

$params['lang'] = 'en';
$enUrl = $path . '?' . http_build_query($params);
?>
<header style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1em;">
    <h2><a href="/index.php"><img src="/includes/logo.png" alt="Logo" style="height:50px;"></a></h2>
    <nav>
        <a href="<?= htmlspecialchars($frUrl) ?>">FR</a> |
        <a href="<?= htmlspecialchars($enUrl) ?>">EN</a>

        <?php if ($currentUser): ?>
            &nbsp;|&nbsp;
            <span>
                <?= __("logged_in_as") ?? "Connecté en tant que" ?>
                <?= htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']) ?>
            </span>
            &nbsp;(<a href="/users/logout.php"><?= __("logout") ?? "Se déconnecter" ?></a>)
        <?php else: ?>
            &nbsp;|&nbsp;
            <a href="/users/login.php"><?= __("login") ?? "Se connecter" ?></a>
            &nbsp;|&nbsp;
            <a href="/users/create.php"><?= __("register") ?? "S'enregistrer" ?></a>
        <?php endif; ?>
    </nav>
</header>
<hr>