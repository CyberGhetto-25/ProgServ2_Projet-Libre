<?php
session_start();

// Détection de la langue via ?lang=fr ou ?lang=en
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = in_array($lang, ['fr', 'en']) ? $lang : 'fr';
}

// Langue par défaut
$lang = $_SESSION['lang'] ?? 'fr';
$langFile = __DIR__ . '/../lang/' . $lang . '.php';

// Chargement du fichier de langue
if (file_exists($langFile)) {
    $translations = require $langFile;
} else {
    $translations = require __DIR__ . '/../lang/fr.php';
}

// Fonction de traduction
function __($key, ...$params)
{
    global $translations;
    if (!isset($translations[$key])) {
        return $key; // Retourne la clé si elle n'existe pas
    }
    $text = $translations[$key];
    if ($params) {
        $text = sprintf($text, ...$params);
    }
    return $text;
}
?>
