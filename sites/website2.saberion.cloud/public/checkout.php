<?php
$site = require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/layout.php';
$pdo = $site['pdo'];

$errors = [];
$form = ['name' => '', 'email' => '', 'address' => ''];

$rows = $pdo ? cart_rows($pdo, $site['cart_id']) : [];
if ($pdo && !$rows) {
    header('Location: /cart.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_post($site);

    $form['name']    = trim((string) ($_POST['name'] ?? ''));
    $form['email']   = trim((string) ($_POST['email'] ?? ''));
    $form['address'] = trim((string) ($_POST['address'] ?? ''));

    if ($form['name'] === '' || mb_strlen($form['name']) > 100)          { $errors[] = 'Please enter your name.'; }
    if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL))               { $errors[] = 'Please enter a valid email address.'; }
    if ($form['address'] === '' || mb_strlen($form['address']) > 255)     { $errors[] = 'Please enter a delivery address.'; }

    if (!$errors) {
        try {
            $pdo->beginTransaction();

            // Prices come from the database, never from the browser.
            $rows  = cart_rows($pdo, $site['cart_id']);
            if (!$rows) { throw new RuntimeException('empty cart'); }
            $total = array_sum(array_map(fn($r) => (float) $r['line_total'], $rows));

            $q = $pdo->prepare("INSERT INTO orders (cart_id, customer_name, email, address, total) VALUES (?, ?, ?, ?, ?)");
            $q->execute([$site['cart_id'], $form['name'], $form['email'], $form['address'], $total]);
            $orderId = (int) $pdo->lastInsertId();

            $item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, name, price, qty) VALUES (?, ?, ?, ?, ?)");
            foreach ($rows as $r) {
                $item->execute([$orderId, $r['id'], $r['name'], $r['price'], $r['qty']]);
            }

            $q = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ?");
            $q->execute([$site['cart_id']]);

            $pdo->commit();
            header('Location: /order.php?id=' . $orderId);
            exit;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            error_log('website2 checkout error: ' . $e->getMessage());
            $errors[] = 'Sorry, we could not place your order. Please try again.';
        }
    }
}

$total = array_sum(array_map(fn($r) => (float) $r['line_total'], $rows));

page_start($site, 'Checkout');
?>
<main class="page"><div class="wrap">
  <h1>Checkout</h1>

  <?php if (!$site['ok']): ?>
    <div class="alert bad"><?= h($site['error']) ?></div>
  <?php else: ?>
    <?php foreach ($errors as $e): ?><div class="alert bad"><?= h($e) ?></div><?php endforeach; ?>

    <div class="two">
      <div class="card">
        <h3>Delivery details</h3>
        <form method="post" action="/checkout.php">
          <input type="hidden" name="csrf" value="<?= h($site['csrf']) ?>">
          <label>Full name<input type="text" name="name" maxlength="100" required value="<?= h($form['name']) ?>"></label>
          <label>Email<input type="email" name="email" maxlength="120" required value="<?= h($form['email']) ?>"></label>
          <label>Delivery address<textarea name="address" rows="3" maxlength="255" required><?= h($form['address']) ?></textarea></label>
          <button class="buy" type="submit">Place order</button>
        </form>
        <p class="note">This is a demo shop: no payment is taken.</p>
      </div>

      <div class="card">
        <h3>Order summary</h3>
        <table class="cart">
          <?php foreach ($rows as $r): ?>
            <tr><td><?= h($r['name']) ?> &times; <?= (int) $r['qty'] ?></td><td>$<?= number_format((float) $r['line_total'], 2) ?></td></tr>
          <?php endforeach; ?>
          <tr class="sum"><td>Total</td><td>$<?= number_format($total, 2) ?></td></tr>
        </table>
      </div>
    </div>
  <?php endif; ?>
</div></main>
<?php page_end($site);
