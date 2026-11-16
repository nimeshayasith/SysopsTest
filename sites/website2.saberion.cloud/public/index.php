<?php
$site = require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/layout.php';

page_start($site, 'Everyday tech, delivered fast');
?>
<header class="hero"><div class="wrap">
  <h1>Everyday tech, delivered fast.</h1>
  <p>Quality accessories, audio and office gear at honest prices. Free delivery on orders over $50.</p>
  <a class="btn" href="#products">Shop now</a>
  <a class="btn alt" href="/about.html">Shipping &amp; returns</a>
</div></header>

<section id="products"><div class="wrap">
  <h2>Featured products</h2>
  <p class="lead">Our most popular items this month.</p>
  <div class="grid">
    <?php foreach ($site['products'] as $p): ?>
      <div class="card">
        <div class="icon"><?= h($p['icon']) ?></div>
        <h3><?= h($p['name']) ?></h3>
        <p><?= h($p['blurb']) ?></p>
        <div class="meta"><span class="tag"><?= h($p['category']) ?></span></div>
        <div class="row">
          <div class="price">$<?= number_format((float) $p['price'], 2) ?></div>
          <form method="post" action="/cart.php">
            <input type="hidden" name="csrf" value="<?= h($site['csrf']) ?>">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
            <button class="buy" type="submit">Add to cart</button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div></section>

<section id="deals" class="tint"><div class="wrap">
  <div class="stats">
    <div><strong>Free</strong>Delivery over $50</div>
    <div><strong>30 days</strong>Easy returns</div>
    <div><strong>2 yrs</strong>Warranty</div>
    <div><strong>4.8★</strong>Customer rating</div>
  </div>
</div></section>
<?php page_end($site);
