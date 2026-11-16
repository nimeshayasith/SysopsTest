<?php
// Website 2 - database logic, session and cart helpers.
// Sits above public/, so it is never served directly.
$cfg = require __DIR__ . '/../config.php';

function h($s): string { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }

session_set_cookie_params(['path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
session_start();
if (empty($_SESSION['csrf']))    { $_SESSION['csrf']    = bin2hex(random_bytes(32)); }
if (empty($_SESSION['cart_id'])) { $_SESSION['cart_id'] = bin2hex(random_bytes(16)); }

$site = [
    'cfg' => $cfg, 'ok' => false, 'pdo' => null, 'message' => '', 'visits' => 0,
    'products' => [], 'error' => '', 'cart_count' => 0,
    'csrf' => $_SESSION['csrf'], 'cart_id' => $_SESSION['cart_id'],
];

try {
    $pdo = new PDO(
        "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['name']};charset=utf8mb4",
        $cfg['user'], $cfg['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
    $site['ok'] = true;
    $site['pdo'] = $pdo;

    $pdo->exec("CREATE TABLE IF NOT EXISTS messages (id INT PRIMARY KEY, body VARCHAR(255) NOT NULL)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS visits (id INT PRIMARY KEY, total INT NOT NULL DEFAULT 0)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL, category VARCHAR(50) NOT NULL, price DECIMAL(10,2) NOT NULL, icon VARCHAR(8) NOT NULL, blurb VARCHAR(160) NOT NULL)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS cart_items (cart_id CHAR(32) NOT NULL, product_id INT NOT NULL, qty INT NOT NULL, added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (cart_id, product_id))");
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (id INT AUTO_INCREMENT PRIMARY KEY, cart_id CHAR(32) NOT NULL, customer_name VARCHAR(100) NOT NULL, email VARCHAR(120) NOT NULL, address VARCHAR(255) NOT NULL, total DECIMAL(10,2) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX (cart_id))");
    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (id INT AUTO_INCREMENT PRIMARY KEY, order_id INT NOT NULL, product_id INT NOT NULL, name VARCHAR(100) NOT NULL, price DECIMAL(10,2) NOT NULL, qty INT NOT NULL, INDEX (order_id))");

    $pdo->exec("INSERT IGNORE INTO messages (id, body) VALUES (1, 'Welcome to Cartly Shop. This message is stored in site2_db.')");
    if ((int) $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn() === 0) {
        $pdo->exec("INSERT INTO products (name, category, price, icon, blurb) VALUES
          ('Wireless Mouse', 'Accessories', 12.50, '🖱️', 'Quiet clicks, 18-month battery life.'),
          ('Mechanical Keyboard', 'Accessories', 59.90, '⌨️', 'Hot-swappable switches with backlight.'),
          ('Laptop Stand', 'Office', 29.00, '💻', 'Aluminium stand, folds flat.'),
          ('USB-C Cable 2m', 'Cables', 6.99, '🔌', 'Fast charging, braided cable.'),
          ('Noise Cancelling Headphones', 'Audio', 89.00, '🎧', '30 hours of playback.'),
          ('Desk Lamp', 'Office', 24.50, '💡', 'Adjustable brightness and colour.')");
    }

    // Only the home page counts as a visit, not cart/checkout actions.
    if (basename($_SERVER['SCRIPT_NAME']) === 'index.php') {
        $pdo->exec("INSERT INTO visits (id, total) VALUES (1, 1) ON DUPLICATE KEY UPDATE total = total + 1");
    }

    $site['message']  = $pdo->query("SELECT body FROM messages WHERE id = 1")->fetchColumn();
    $site['visits']   = (int) $pdo->query("SELECT total FROM visits WHERE id = 1")->fetchColumn();
    $site['products'] = $pdo->query("SELECT id, name, category, price, icon, blurb FROM products ORDER BY id")->fetchAll();

    $q = $pdo->prepare("SELECT COALESCE(SUM(qty), 0) FROM cart_items WHERE cart_id = ?");
    $q->execute([$site['cart_id']]);
    $site['cart_count'] = (int) $q->fetchColumn();
} catch (Throwable $e) {
    error_log('website2 DB error: ' . $e->getMessage());
    $site['error'] = 'Database connection failed.';
}

return $site;
