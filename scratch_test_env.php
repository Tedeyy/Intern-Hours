<?php
require_once __DIR__ . '/config.php';

echo "SECRET_KEY in ENV: " . ($_ENV['SECRET_KEY'] ?? 'MISSING') . "\n";
echo "SECRET_KEY in SERVER: " . ($_SERVER['SECRET_KEY'] ?? 'MISSING') . "\n";
echo "SECRET_KEY from getenv: " . (getenv('SECRET_KEY') ?: 'MISSING') . "\n";
