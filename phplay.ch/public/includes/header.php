<?php
require_once __DIR__ . '/lang.php';
require_once __DIR__ . '/auth.php';

$currentUser = current_user();
?>
<header style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1em;">
    <h2><a href="/index.php"><img src="/phplay.ch/public/includes/logo.png"></a></h2>
    <nav>
        <a href="?lang=fr">FR</a> |
        <a href="?lang=en">EN</a>

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
        <?php endif; ?>
    </nav>
</header>
<hr>

