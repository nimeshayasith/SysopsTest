<?php
// Website 1 - database logic. Sits above public/, so it is never served directly.
$cfg = require __DIR__ . '/../config.php';

function h($s): string { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }

$site = ['cfg' => $cfg, 'ok' => false, 'message' => '', 'visits' => 0, 'news' => [], 'error' => ''];

try {
    $pdo = new PDO(
        "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['name']};charset=utf8mb4",
        $cfg['user'], $cfg['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]
    );
    $site['ok'] = true;

    $pdo->exec("CREATE TABLE IF NOT EXISTS messages (id INT PRIMARY KEY, body VARCHAR(255) NOT NULL)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS visits (id INT PRIMARY KEY, total INT NOT NULL DEFAULT 0)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS news (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(150) NOT NULL, body VARCHAR(400) NOT NULL, posted DATE NOT NULL)");

    $pdo->exec("INSERT IGNORE INTO messages (id, body) VALUES (1, 'Welcome to Northwind Digital. This message is stored in site1_db.')");
    if ((int) $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn() === 0) {
        $pdo->exec("INSERT INTO news (title, body, posted) VALUES
          ('We are now an AWS Select Partner', 'Our team has completed advanced AWS certifications to serve you better.', '2026-10-01'),
          ('New Singapore data centre support', 'All new hosting plans now launch in the ap-southeast-1 region for lower latency.', '2026-09-12'),
          ('Free security review for new clients', 'Book a free 30 minute review of your current hosting set-up.', '2026-08-20')");
    }

    // Each page load counts as one visit.
    $pdo->exec("INSERT INTO visits (id, total) VALUES (1, 1) ON DUPLICATE KEY UPDATE total = total + 1");

    $site['message'] = $pdo->query("SELECT body FROM messages WHERE id = 1")->fetchColumn();
    $site['visits']  = (int) $pdo->query("SELECT total FROM visits WHERE id = 1")->fetchColumn();
    $site['news']    = $pdo->query("SELECT title, body, posted FROM news ORDER BY posted DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('website1 DB error: ' . $e->getMessage());
    $site['error'] = 'Database connection failed.';
}

return $site;
