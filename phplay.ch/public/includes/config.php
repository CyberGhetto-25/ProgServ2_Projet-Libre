<?php
const DATABASE_CONFIGURATION_FILE = __DIR__ . '/../../src/config/database.ini';
$config = parse_ini_file(DATABASE_CONFIGURATION_FILE, true);
if (!$config) {
    throw new Exception("Erreur lors de la lecture du fichier de configuration : " . DATABASE_CONFIGURATION_FILE);
}
$host = $config['host'];
$port = $config['port'];
$database = $config['database'];
$username = $config['username'];
$password = $config['password'];

// Connexion PDO
$pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Création de la base de données si elle n'existe pas
$sql = "CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;";
$pdo->exec($sql);

// Sélection de la base de données
$pdo->exec("USE `$database`;");

// Création des tables si elles n'existent pas
$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        age INT NOT NULL
    );
");

$pdo->exec("
    CREATE TABLE IF NOT EXISTS playlists (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        playlist_name VARCHAR(255) NOT NULL,
        is_public BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );
");

$pdo->exec("
    CREATE TABLE IF NOT EXISTS tracks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        artist VARCHAR(255) NOT NULL,
        genre VARCHAR(100),
        duration INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
");

$pdo->exec("
    CREATE TABLE IF NOT EXISTS playlist_tracks (
        playlist_id INT NOT NULL,
        track_id INT NOT NULL,
        PRIMARY KEY (playlist_id, track_id),
        FOREIGN KEY (playlist_id) REFERENCES playlists(id) ON DELETE CASCADE,
        FOREIGN KEY (track_id) REFERENCES tracks(id) ON DELETE CASCADE
    );
");
