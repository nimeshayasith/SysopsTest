<?php
$site = require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/layout.php';
$pdo = $site['pdo'];

// Add, update or remove an item, then redirect so a refresh does not repeat it.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_post($site);
    $action = (string) ($_POST['action'] ?? '');
    $pid    = (int) ($_POST['product_id'] ?? 0);
    $qty    = max(0, min(10, (int) ($_POST['qty'] ?? 1)));

    $exists = $pdo->prepare("SELECT COUNT(*) FROM products WHERE id = ?");
    $exists->execute([$pid]);

    if ($exists->fetchColumn()) {
        if ($action === 'add') {
            $q = $pdo->prepare("INSERT INTO cart_items (cart_id, product_id, qty) VALUES (?, ?, 1)
                                ON DUPLICATE KEY UPDATE qty = LEAST(qty + 1, 10)");
            $q->execute([$site['cart_id'], $pid]);
        } elseif ($action === 'update' && $qty > 0) {
            $q = $pdo->prepare("UPDATE cart_items SET qty = ? WHERE cart_id = ? AND product_id = ?");
            $q->execute([$qty, $site['cart_id'], $pid]);
        } elseif ($action === 'remove' || ($action === 'update' && $qty === 0)) {
            $q = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?");
            $q->execute([$site['cart_id'], $pid]);
        }
    }
    header('Location: /cart.php');
    exit;
}

$rows  = $pdo ? cart_rows($pdo, $site['cart_id']) : [];
$total = array_sum(array_map(fn($r) => (float) $r['line_total'], $rows));

page_start($site, 'Your cart');
?>
<main class="page"><div class="wrap">
  <h1>Your cart</h1>

  <?php if (!$site['ok']): ?>
    <div class="alert bad"><?= h($site['error']) ?></div>
  <?php elseif (!$rows): ?>
    <div class="card">
      <p>Your cart is empty.</p>
      <p style="margin-top:12px"><a class="btn solid" href="/">Continue shopping</a></p>
    </div>
  <?php else: ?>
    <div class="card">
      <table class="cart">
        <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Total</th><th></th></tr>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= h($r['icon']) ?> <?= h($r['name']) ?></td>
            <td>$<?= number_format((float) $r['price'], 2) ?></td>
            <td>
              <form method="post" action="/cart.php" class="inline">
                <input type="hidden" name="csrf" value="<?= h($site['csrf']) ?>">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="product_id" value="<?= (int) $r['id'] ?>">
                <input class="qty" type="number" name="qty" min="0" max="10" value="<?= (int) $r['qty'] ?>">
                <button class="buy small" type="submit">Update</button>
              </form>
            </td>
            <td>$<?= number_format((float) $r['line_total'], 2) ?></td>
            <td>
              <form method="post" action="/cart.php" class="inline">
                <input type="hidden" name="csrf" value="<?= h($site['csrf']) ?>">
                <input type="hidden" name="action" value="remove">
                <input type="hidden" name="product_id" value="<?= (int) $r['id'] ?>">
                <button class="link" type="submit">Remove</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <tr class="sum"><td colspan="3">Order total</td><td colspan="2">$<?= number_format($total, 2) ?></td></tr>
      </table>
    </div>
    <p>
      <a href="/">&larr; Continue shopping</a>
      &nbsp;&nbsp;
      <a class="btn solid" href="/checkout.php">Proceed to checkout</a>
    </p>
  <?php endif; ?>
</div></main>
<?php page_end($site);
