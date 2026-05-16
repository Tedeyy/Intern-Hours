<?php
// Load environment variables
// Load environment variables manually if .env exists
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            // Remove quotes if present
            $value = trim($value, '"\'');
            
            if (!isset($_ENV[$name])) {
                $_ENV[$name] = $value;
            }
            if (!isset($_SERVER[$name])) {
                $_SERVER[$name] = $value;
            }
            // putenv might be disabled on some shared hosts
            @putenv("$name=$value");
        }
    }
}

// Function to get config with fallbacks
function get_config($key, $default = '') {
    return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
}

$host = get_config('DB_HOST', 'localhost');
$port = get_config('DB_PORT', '3306');
$dbname = get_config('DB_CONFIG_NAME', get_config('DB_NAME', 'intern_hours_db'));
$username = get_config('DB_USER', 'root');
$password = get_config('DB_PASS', '');

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Auto-migration: Ensure columns exist
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS is_public BOOLEAN DEFAULT FALSE");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS is_darkmode BOOLEAN DEFAULT FALSE");
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
