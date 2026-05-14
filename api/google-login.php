<?php
require_once __DIR__ . '/../config.php';

session_start();

$google_client_id = getenv('GOOGLE_CLIENT_ID');
$google_client_secret = getenv('GOOGLE_CLIENT_SECRET');
$google_redirect_uri = getenv('GOOGLE_REDIRECT_URI') ?: 'http://localhost/Intern-Hours/auth/google-callback.php';

if (empty($google_client_id) || empty($google_client_secret)) {
    die('Google OAuth credentials not configured. Please set GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET in .env file.');
}

// Generate random state for CSRF protection
$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

// Google OAuth URL
$auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
    'client_id' => $google_client_id,
    'redirect_uri' => $google_redirect_uri,
    'response_type' => 'code',
    'scope' => 'openid email profile',
    'state' => $state,
]);

header('Location: ' . $auth_url);
exit;
?>
