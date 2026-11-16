<?php
$site = require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/layout.php';
$pdo = $site['pdo'];

$order = null;
$items = [];
if ($pdo) {
    // Only the visitor who placed the order can view it.
    $q = $pdo->prepare("SELECT id, customer_name, email, address, total, created_at FROM orders WHERE id = ? AND cart_id = ?");
    $q->execute([(int) ($_GET['id'] ?? 0), $site['cart_id']]);
    $order = $q->fetch();
    if ($order) {
        $q = $pdo->prepare("SELECT name, price, qty FROM order_items WHERE order_id = ? ORDER BY id");
        $q->execute([$order['id']]);
        $items = $q->fetchAll();
    }
}

if ($pdo && !$order) { http_response_code(404); }

page_start($site, 'Order confirmation');
?>
<main class="page"><div class="wrap">
  <?php if (!$site['ok']): ?>
    <div class="alert bad"><?= h($site['error']) ?></div>
  <?php elseif (!$order): ?>
    <h1>Order not found</h1>
    <p><a href="/">&larr; Back to the shop</a></p>
  <?php else: ?>
    <h1>Thank you, <?= h($order['customer_name']) ?>!</h1>
    <div class="alert ok">Your order #<?= (int) $order['id'] ?> has been placed and saved.</div>

    <div class="card">
      <table class="cart">
        <tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th></tr>
        <?php foreach ($items as $i): ?>
          <tr>
            <td><?= h($i['name']) ?></td>
            <td>$<?= number_format((float) $i['price'], 2) ?></td>
            <td><?= (int) $i['qty'] ?></td>
            <td>$<?= number_format((float) $i['price'] * (int) $i['qty'], 2) ?></td>
          </tr>
        <?php endforeach; ?>
        <tr class="sum"><td colspan="3">Order total</td><td>$<?= number_format((float) $order['total'], 2) ?></td></tr>
      </table>
    </div>
    <div class="card">
      <h3>Delivery to</h3>
      <p><?= nl2br(h($order['address'])) ?><br>Confirmation sent to <?= h($order['email']) ?></p>
      <p class="note">Placed <?= h($order['created_at']) ?>. Demo shop: no payment taken.</p>
    </div>
    <p><a href="/">&larr; Continue shopping</a></p>
  <?php endif; ?>
</div></main>
<?php page_end($site);
