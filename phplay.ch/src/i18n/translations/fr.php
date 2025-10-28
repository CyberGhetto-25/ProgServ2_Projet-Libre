<?php
return [
    // ========== Page d'accueil (index.php) ==========
    'home_title' => 'Gestion de mes morceaux | PHPlay',
    'home_heading' => 'PhPlay - Accueil',
    'home_subheading' => '🎧 PhPlay',
    'new_user_button' => '👤 Nouvel utilisateur',
    'new_playlist_button' => '➕ Nouvelle playlist',
    'users_section' => 'Utilisateurs',
    'no_users' => 'Aucun utilisateur pour le moment.',
    'playlists_section' => 'Playlists',
    'no_playlists' => 'Aucune playlist pour le moment.',
    'playlist_name' => 'Nom de la playlist',
    'created_by' => 'Créée par',
    'visibility' => 'Visibilité',
    'public' => 'Publique',
    'private' => 'Privée',
    'view_playlist' => '🎵 Voir',

    // ========== Page de création d'utilisateur (create.php) ==========
    'create_user_title' => 'Créer un.e nouvel.le utilisateur.trice | PHPlay',
    'create_user_heading' => 'Créer un.e nouvel.le utilisateur.trice',
    'first_name' => 'Prénom',
    'last_name' => 'Nom',
    'email' => 'E-mail',
    'age' => 'Âge',
    'create_button' => 'Créer',
    'success_message' => 'Le formulaire a été soumis avec succès !',
    'error_message' => 'Le formulaire contient des erreurs :',
    'first_name_error' => 'Le prénom doit contenir au moins 2 caractères.',
    'last_name_error' => 'Le nom doit contenir au moins 2 caractères.',
    'email_error' => 'Un email valide est requis.',
    'age_error' => "L'âge doit être un nombre positif.",

    // ========== Page de création de playlist (create_playlist.php) ==========
    'create_playlist_title' => 'Créer une playlist | PHPlay',
    'create_playlist_heading' => 'Créer une nouvelle playlist',
    'playlist_name_label' => 'Nom de la playlist',
    'user_label' => 'Utilisateur',
    'select_user' => '-- Sélectionnez un utilisateur --',
    'is_public_label' => 'Playlist publique',
    'save_playlist_button' => 'Créer',
    'playlist_created' => 'La playlist a été créée avec succès 🎵',
    'playlist_name_error' => 'Le nom de la playlist doit contenir au moins 2 caractères.',
    'user_required_error' => 'Vous devez sélectionner un utilisateur.',

    // ========== Page d'une playlist (playlist.php) ==========
    'playlist_title' => '%s | PHPlay', // %s sera remplacé par le nom de la playlist
    'tracks_section' => '🎵 Morceaux de la playlist',
    'no_tracks' => 'Aucun morceau dans cette playlist pour le moment.',
    'track_title' => 'Titre',
    'track_artist' => 'Artiste',
    'track_genre' => 'Genre',
    'track_duration' => 'Durée (s)',
    'back_home' => '⬅ Retour à l’accueil',
    'add_track_button' => '➕ Ajouter un morceau',

    // ========== Page d'ajout de morceau (create_track.php) ==========
    'add_track_title' => 'Ajouter un morceau | PHPlay',
    'add_track_heading' => 'Ajouter un morceau à la playlist',
    'track_title_label' => 'Titre du morceau *',
    'track_artist_label' => 'Artiste *',
    'track_genre_label' => 'Genre',
    'track_duration_label' => 'Durée (secondes)',
    'add_track_button' => 'Ajouter à la playlist',
    'back_to_playlist' => 'Retour à la playlist',
    'track_added' => 'Le morceau a été ajouté avec succès',
    'title_error' => 'Le titre doit contenir au moins 2 caractères.',
    'artist_error' => "L'artiste doit contenir au moins 2 caractères.",
    'duration_error' => 'La durée doit être un nombre positif (en secondes).',

    // ========== Messages génériques ==========
    'required_field' => 'Ce champ est obligatoire.',
    'min_length' => 'Doit contenir au moins %d caractères.', // %d sera remplacé par le nombre
];
?>