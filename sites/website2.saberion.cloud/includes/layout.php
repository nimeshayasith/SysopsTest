<?php
// Shared page header and footer for the dynamic pages of Website 2.

function page_start(array $site, string $title): void
{
    $n = (int) $site['cart_count'];
    ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($title) ?> | Cartly Shop</title>
<link rel="stylesheet" href="/assets/style.css">
</head>
<body>

<nav class="nav"><div class="wrap">
  <a class="brand" href="/">Cartly<span> Shop</span></a>
  <ul>
    <li><a href="/">Shop</a></li>
    <li><a href="/about.html">Shipping &amp; Help</a></li>
    <li><a href="/cart.php">🛒 Cart (<?= $n ?>)</a></li>
  </ul>
</div></nav>
<?php
}

function page_end(array $site): void
{
    $cfg = $site['cfg'];
    ?>
<footer><div class="wrap">
  <strong style="color:#fff">Cartly Shop</strong><br>
  Everyday tech &middot; <a href="/about.html">Shipping &amp; help</a>

  <div class="status">
    <strong>System status</strong><br>
    <?php if ($site['ok']): ?>
      Database: <span class="ok">Connected</span><br>
      Site: <?= h($cfg['site_name']) ?> (<?= h($cfg['name']) ?>)<br>
      Message: <?= h($site['message']) ?><br>
      Visit count: <strong><?= (int) $site['visits'] ?></strong>
    <?php else: ?>
      Database: <span class="bad"><?= h($site['error']) ?></span>
    <?php endif; ?>
  </div>
  <div class="copy">&copy; <?= date('Y') ?> Cartly Shop. All rights reserved.</div>
</div></footer>

</body>
</html>
<?php
}

// Stops a request that is not a valid POST from this visitor's session.
function require_valid_post(array $site): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST'
        || !hash_equals($site['csrf'], (string) ($_POST['csrf'] ?? ''))) {
        http_response_code(400);
        exit('Invalid request.');
    }
    if (!$site['pdo']) {
        http_response_code(503);
        exit('Service unavailable.');
    }
}

// Cart rows for this visitor, with prices read from the database.
function cart_rows(PDO $pdo, string $cartId): array
{
    $q = $pdo->prepare(
        "SELECT p.id, p.name, p.icon, p.price, c.qty, (p.price * c.qty) AS line_total
           FROM cart_items c JOIN products p ON p.id = c.product_id
          WHERE c.cart_id = ? ORDER BY p.id"
    );
    $q->execute([$cartId]);
    return $q->fetchAll();
}
