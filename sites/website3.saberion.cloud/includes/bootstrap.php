<?php
// Website 3 - database logic. Sits above public/, so it is never served directly.
$cfg = require __DIR__ . '/../config.php';

function h($s): string { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }

$site = ['cfg' => $cfg, 'ok' => false, 'message' => '', 'visits' => 0, 'courses' => [], 'error' => ''];

try {
    $pdo = new PDO(
        "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['name']};charset=utf8mb4",
        $cfg['user'], $cfg['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]
    );
    $site['ok'] = true;

    $pdo->exec("CREATE TABLE IF NOT EXISTS messages (id INT PRIMARY KEY, body VARCHAR(255) NOT NULL)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS visits (id INT PRIMARY KEY, total INT NOT NULL DEFAULT 0)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS courses (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(100) NOT NULL, level VARCHAR(30) NOT NULL, weeks INT NOT NULL, instructor VARCHAR(60) NOT NULL, icon VARCHAR(8) NOT NULL, summary VARCHAR(200) NOT NULL)");

    $pdo->exec("INSERT IGNORE INTO messages (id, body) VALUES (1, 'Welcome to BrightPath Learning. This message is stored in site3_db.')");
    if ((int) $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn() === 0) {
        $pdo->exec("INSERT INTO courses (title, level, weeks, instructor, icon, summary) VALUES
          ('Linux Fundamentals', 'Beginner', 4, 'A. Perera', '🐧', 'Command line, users, permissions and package management.'),
          ('AWS Cloud Essentials', 'Beginner', 6, 'N. Silva', '☁️', 'VPC, EC2, RDS and IAM from the ground up.'),
          ('Nginx and PHP Hosting', 'Intermediate', 5, 'K. Fernando', '🌐', 'Host multiple PHP websites securely on one server.'),
          ('MySQL Administration', 'Intermediate', 6, 'S. Jayasinghe', '🗄️', 'Users, backups, replication and performance basics.')");
    }

    $pdo->exec("INSERT INTO visits (id, total) VALUES (1, 1) ON DUPLICATE KEY UPDATE total = total + 1");

    $site['message'] = $pdo->query("SELECT body FROM messages WHERE id = 1")->fetchColumn();
    $site['visits']  = (int) $pdo->query("SELECT total FROM visits WHERE id = 1")->fetchColumn();
    $site['courses'] = $pdo->query("SELECT title, level, weeks, instructor, icon, summary FROM courses ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('website3 DB error: ' . $e->getMessage());
    $site['error'] = 'Database connection failed.';
}

return $site;
