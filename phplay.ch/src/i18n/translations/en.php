<?php
return [
    // ========== Home Page (index.php) ==========
    'home_title' => 'Manage my tracks | PHPlay',
    'home_heading' => 'PHPlay - Home',
    'home_subheading' => '🎧 PHPlay',
    'new_user_button' => '👤 New user',
    'new_playlist_button' => '➕ New playlist',
    'users_section' => 'Users',
    'no_users' => 'No users yet.',
    'playlists_section' => 'Playlists',
    'no_playlists' => 'No playlists yet.',
    'playlist_name' => 'Playlist name',
    'created_by' => 'Created by',
    'visibility' => 'Visibility',
    'public' => 'Public',
    'private' => 'Private',
    'view_playlist' => '🎵 View',

    // ========== Create User Page (create.php) ==========
    'create_user_title' => 'Create a new user | PHPlay',
    'create_user_heading' => 'Create a new user',
    'first_name' => 'First name',
    'last_name' => 'Last name',
    'email' => 'Email',
    'age' => 'Age',
    'create_button' => 'Create',
    'success_message' => 'The form was submitted successfully!',
    'error_message' => 'The form contains errors:',
    'first_name_error' => 'The first name must contain at least 2 characters.',
    'last_name_error' => 'The last name must contain at least 2 characters.',
    'email_error' => 'A valid email is required.',
    'age_error' => 'Age must be a positive number.',

    // ========== Create Playlist Page (create_playlist.php) ==========
    'create_playlist_title' => 'Create a playlist | PHPlay',
    'create_playlist_heading' => 'Create a new playlist',
    'playlist_name_label' => 'Playlist name',
    'user_label' => 'User',
    'select_user' => '-- Select a user --',
    'is_public_label' => 'Public playlist',
    'save_playlist_button' => 'Create',
    'playlist_created' => 'The playlist was created successfully 🎵',
    'playlist_name_error' => 'The playlist name must contain at least 2 characters.',
    'user_required_error' => 'You must select a user.',

    // ========== Playlist Page (playlist.php) ==========
    'playlist_title' => '%s | PHPlay', // %s will be replaced by the playlist name
    'tracks_section' => '🎵 Playlist tracks',
    'no_tracks' => 'No tracks in this playlist yet.',
    'track_title' => 'Title',
    'track_artist' => 'Artist',
    'track_genre' => 'Genre',
    'track_duration' => 'Duration (s)',
    'back_home' => '⬅ Back to home',
    'add_track_button' => '➕ Add a track',

    // ========== Add Track Page (create_track.php) ==========
    'add_track_title' => 'Add a track | PHPlay',
    'add_track_heading' => 'Add a track to the playlist',
    'track_title_label' => 'Track title *',
    'track_artist_label' => 'Artist *',
    'track_genre_label' => 'Genre',
    'track_duration_label' => 'Duration (seconds)',
    'add_track_button' => 'Add to playlist',
    'back_to_playlist' => 'Back to playlist',
    'track_added' => 'The track was added successfully',
    'title_error' => 'The title must contain at least 2 characters.',
    'artist_error' => 'The artist must contain at least 2 characters.',
    'duration_error' => 'The duration must be a positive number (in seconds).',

    // ========== Generic Messages ==========
    'required_field' => 'This field is required.',
    'min_length' => 'Must contain at least %d characters.', // %d will be replaced by the number
];
