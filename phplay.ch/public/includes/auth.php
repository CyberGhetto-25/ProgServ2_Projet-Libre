<?php
function login_user(array $user): void
{
    // On garde seulement ce qui est utile
    $_SESSION['user'] = [
        'id'         => $user['id'],
        'first_name' => $user['first_name'] ?? '',
        'last_name'  => $user['last_name'] ?? '',
        'email'      => $user['email'] ?? '',
        'role'       => $user['role'] ?? 'user',
    ];
}

/**
 * Déconnecte l'utilisateur.
 */
function logout_user(): void
{
    unset($_SESSION['user']);
}

//True si quelqu'un est connecté.
function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

/**
 * Retourne l'utilisateur courant ou null.
 */
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_superadmin(): bool
{
    $u = current_user();
    return $u && ($u['role'] ?? 'user') === 'superadmin';
}
/**
 * Exige qu'un utilisateur soit connecté, sinon redirige vers la page de login.
 */
function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: /users/login.php');
        exit;
    }
}
